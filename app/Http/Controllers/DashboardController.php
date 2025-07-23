<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timesheet;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        $data = [
            'user' => $user,
            'todayTimesheet' => $user->timesheets()->whereDate('date', $today)->first(),
            'weeklyHours' => $this->getWeeklyHours($user),
            'monthlyHours' => $this->getMonthlyHours($user),
            'activeProjects' => $user->projects()->where('status', 'active')->count(),
            'pendingLeaves' => $user->leaves()->where('status', Leave::STATUS_PENDING)->count(),
        ];

        if ($user->isManagerOrAdmin()) {
            $data = array_merge($data, [
                'totalUsers' => User::count(),
                'totalProjects' => Project::count(),
                'pendingLeaveRequests' => Leave::where('status', Leave::STATUS_PENDING)->count(),
                'recentTimesheets' => Timesheet::with(['user', 'project'])
                    ->whereDate('date', $today)
                    ->latest()
                    ->take(10)
                    ->get(),
            ]);
        }

        return view('dashboard', $data);
    }

    private function getWeeklyHours($user)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        return $user->timesheets()
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get()
            ->sum(function ($timesheet) {
                return $timesheet->getWorkedHours();
            });
    }

    private function getMonthlyHours($user)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return $user->timesheets()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum(function ($timesheet) {
                return $timesheet->getWorkedHours();
            });
    }
}