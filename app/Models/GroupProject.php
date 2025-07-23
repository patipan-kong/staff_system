<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'color',
        'icon',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}