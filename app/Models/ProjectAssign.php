<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'role',
        'assigned_date',
        'estimated_hours',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'estimated_hours' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}