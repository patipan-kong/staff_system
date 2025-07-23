<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'is_recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public static function isHoliday($date)
    {
        return self::whereDate('date', $date)->exists();
    }

    public static function getHolidaysForMonth($year, $month)
    {
        return self::whereYear('date', $year)
                  ->whereMonth('date', $month)
                  ->get();
    }
}