<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WhiteboardController extends Controller
{
    public function index()
    {
        // Get yesterday and today dates
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Get all users with their timesheets for yesterday and today
        $users = User::with(['timesheets' => function ($query) use ($yesterday, $today) {
            $query->whereDate('date', '>=', $yesterday)
                  ->whereDate('date', '<=', $today)
                  ->with(['project'])
                  ->orderBy('date', 'desc')
                  ->orderBy('start_time', 'asc');
        }, 'department'])
        ->orderBy('name')
        ->get();
        
        // Get total hours for each user
        foreach ($users as $user) {
            $user->yesterdayHours = $user->timesheets
                ->filter(function ($timesheet) use ($yesterday) {
                    return $timesheet->date->isSameDay($yesterday);
                })
                ->sum(function ($timesheet) {
                    return $timesheet->getWorkedHours();
                });
                
            $user->todayHours = $user->timesheets
                ->filter(function ($timesheet) use ($today) {
                    return $timesheet->date->isSameDay($today);
                })
                ->sum(function ($timesheet) {
                    // For ongoing timesheets, calculate current hours
                    if (!$timesheet->end_time && $timesheet->start_time) {
                        $start = Carbon::parse($timesheet->start_time);
                        $now = Carbon::now();
                        $minutesWorked = $now->diffInMinutes($start);
                        $breakMinutes = $timesheet->break_minutes ?? 0;
                        return max(0, ($minutesWorked - $breakMinutes) / 60);
                    }
                    return $timesheet->getWorkedHours();
                });
        }
        
        return view('whiteboard.index', compact('users', 'today', 'yesterday'));
    }
}
