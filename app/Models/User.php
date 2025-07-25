<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'role',
        'department_id',
        'position',
        'salary',
        'photo',
        'hire_date',
        'phone',
        'address',
        'vacation_quota',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function projectAssigns()
    {
        return $this->hasMany(ProjectAssign::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_assigns');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function isAdmin()
    {
        return $this->role === 99;
    }

    public function isManager()
    {
        return $this->role >= 2;
    }

    public function isManagerOrAdmin()
    {
        return ($this->role >= 2 || $this->role === 99);
    }

    public function isStaff()
    {
        return $this->role >= 0;
    }

    public function getRoleName()
    {
        switch ($this->role) {
            case 0: return 'Staff';
            case 1: return 'Leader';
            case 2: return 'Manager';
            case 3: return 'Test';
            case 99: return 'Admin';
            default: return 'Unknown';
        }
    }

    public function staffPlannings()
    {
        return $this->hasMany(StaffPlanning::class);
    }

    public function createdPlannings()
    {
        return $this->hasMany(StaffPlanning::class, 'created_by');
    }
}