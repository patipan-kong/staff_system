<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'group_project_id',
        'estimated_hours',
        'actual_hours',
        'budget',
        'status',
        'start_date',
        'end_date',
        'priority',
        'icon',
        'color',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'budget' => 'decimal:2',
    ];

    public function groupProject()
    {
        return $this->belongsTo(GroupProject::class);
    }

    public function projectAssigns()
    {
        return $this->hasMany(ProjectAssign::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_assigns');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function getTotalCost()
    {
        return $this->timesheets->sum(function ($timesheet) {
            $hours = $timesheet->getWorkedHours();
            $hourlyRate = $timesheet->user->salary ? ($timesheet->user->salary / 160) : 0; // Assuming 160 hours per month
            return $hours * $hourlyRate;
        });
    }

    public function getProgress()
    {
        if ($this->estimated_hours == 0) return 0;
        return min(100, ($this->actual_hours / $this->estimated_hours) * 100);
    }
}