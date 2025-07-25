<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'planned_date',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'planned_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
