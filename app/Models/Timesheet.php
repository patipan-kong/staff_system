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
        
        return ($totalMinutes - $breakMinutes) / 60;
    }

    public function getWorkedTime()
    {
        $hours = $this->getWorkedHours();
        $wholeHours = floor($hours);
        $minutes = round(($hours - $wholeHours) * 60);
        
        return sprintf('%02d:%02d', $wholeHours, $minutes);
    }
}