<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'start_time',
        'end_time',
        'break_minutes',
        'description',
        'attachment',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_minutes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getWorkedHours()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        $totalMinutes = $end->diffInMinutes($start);
        $breakMinutes = $this->break_minutes ?? 0;

        return round(($totalMinutes - $breakMinutes) / 60, 2);
    }

    /**
     * Check if this timesheet overlaps with any existing timesheets for the same user
     */
    public static function hasOverlap($userId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = self::where('user_id', $userId)
            ->where('date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                // Check for any overlap: new start is before existing end AND new end is after existing start
                $q->where(function ($subQ) use ($startTime, $endTime) {
                    $subQ->where('start_time', '<', $endTime)
                         ->where('end_time', '>', $startTime);
                });
            });

        // Exclude the current timesheet when updating
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get overlapping timesheets for a user on a specific date and time range
     */
    public static function getOverlappingTimesheets($userId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = self::with('project')
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($subQ) use ($startTime, $endTime) {
                    $subQ->where('start_time', '<', $endTime)
                         ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    public function getWorkedTime()
    {
        $hours = $this->getWorkedHours();
        $wholeHours = floor($hours);
        $minutes = round(($hours - $wholeHours) * 60);
        
        return sprintf('%02d:%02d', $wholeHours, $minutes);
    }
}