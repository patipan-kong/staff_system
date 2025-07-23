<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Project;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show weekly distribution report
     */
    public function weeklyDistribution(Request $request)
    {
        // Get the week start date (default to current week)
        $weekStart = $request->get('week', Carbon::now()->startOfWeek());
        if (is_string($weekStart)) {
            $weekStart = Carbon::parse($weekStart)->startOfWeek();
        } else {
            $weekStart = Carbon::parse($weekStart)->startOfWeek();
        }
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Get all users for the week
        $users = User::with(['department'])->orderBy('name')->get();
        
        // Get timesheets for the week  
        $timesheetsRaw = Timesheet::with(['user', 'project', 'project.groupProject'])
            ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();
            
        // Group manually to avoid array key issues
        $timesheets = collect();
        foreach ($timesheetsRaw as $timesheet) {
            $userId = $timesheet->user_id;
            $dateStr = $timesheet->date instanceof \Carbon\Carbon ? $timesheet->date->format('Y-m-d') : $timesheet->date;
            
            if (!$timesheets->has($userId)) {
                $timesheets->put($userId, collect());
            }
            
            if (!$timesheets->get($userId)->has($dateStr)) {
                $timesheets->get($userId)->put($dateStr, collect());
            }
            
            $timesheets->get($userId)->get($dateStr)->push($timesheet);
        }
        
        // Get leaves for the week
        $leaves = Leave::with(['user'])
            ->where('status', 'approved')
            ->where(function($query) use ($weekStart, $weekEnd) {
                $query->whereBetween('start_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhere(function($q) use ($weekStart, $weekEnd) {
                          $q->where('start_date', '<=', $weekStart->format('Y-m-d'))
                            ->where('end_date', '>=', $weekEnd->format('Y-m-d'));
                      });
            })
            ->get()
            ->groupBy('user_id');
        
        // Get active projects for context
        $activeProjects = Project::with(['groupProject'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        // Generate week days
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = $weekStart->copy()->addDays($i);
        }
        
        // Calculate weekly statistics
        $weeklyStats = $this->calculateWeeklyStats($timesheets, $leaves);
        
        return view('reports.weekly-distribution', compact(
            'weekStart',
            'weekEnd',
            'weekDays',
            'users',
            'timesheets',
            'leaves',
            'activeProjects',
            'weeklyStats'
        ));
    }
    
    /**
     * Calculate weekly statistics
     */
    private function calculateWeeklyStats($timesheets, $leaves)
    {
        $stats = [
            'total_hours' => 0,
            'total_entries' => 0,
            'users_with_time' => 0,
            'users_on_leave' => 0,
            'project_distribution' => [],
            'daily_totals' => []
        ];
        
        $usersWithTime = [];
        $projectHours = [];
        
        foreach ($timesheets as $userId => $userTimesheets) {
            $usersWithTime[$userId] = true;
            
            foreach ($userTimesheets as $date => $dayTimesheets) {
                $dayTotal = 0;
                
                foreach ($dayTimesheets as $timesheet) {
                    if (!$timesheet) continue;
                    
                    $hours = method_exists($timesheet, 'getWorkedHours') ? $timesheet->getWorkedHours() : 0;
                    $stats['total_hours'] += $hours;
                    $stats['total_entries']++;
                    $dayTotal += $hours;
                    
                    // Track project distribution
                    $projectName = 'Unknown';
                    if ($timesheet->project && isset($timesheet->project->name)) {
                        $projectName = $timesheet->project->name;
                    }
                    
                    if (!isset($projectHours[$projectName])) {
                        $projectHours[$projectName] = 0;
                    }
                    $projectHours[$projectName] += $hours;
                }
                
                // Track daily totals
                if (!isset($stats['daily_totals'][$date])) {
                    $stats['daily_totals'][$date] = 0;
                }
                $stats['daily_totals'][$date] += $dayTotal;
            }
        }
        
        $stats['users_with_time'] = count($usersWithTime);
        $stats['users_on_leave'] = is_countable($leaves) ? $leaves->count() : 0;
        
        // Sort project distribution by hours
        arsort($projectHours);
        $stats['project_distribution'] = array_slice($projectHours, 0, 10, true);
        
        return $stats;
    }
    
    /**
     * Get timesheet data for a specific user and date (AJAX)
     */
    public function getTimesheetData(Request $request)
    {
        $userId = $request->get('user_id');
        $date = $request->get('date');
        
        $timesheets = Timesheet::with(['project', 'project.groupProject'])
            ->where('user_id', $userId)
            ->where('date', $date)
            ->get();
        
        $totalHours = $timesheets->sum(function($ts) {
            return $ts->getWorkedHours();
        });
        
        return response()->json([
            'timesheets' => $timesheets->map(function($ts) {
                return [
                    'id' => $ts->id,
                    'project_name' => $ts->project->name ?? 'Unknown',
                    'group_name' => $ts->project->groupProject->name ?? null,
                    'group_color' => $ts->project->groupProject->color ?? '#6c757d',
                    'start_time' => $ts->start_time,
                    'end_time' => $ts->end_time,
                    'break_duration' => $ts->break_minutes,
                    'worked_hours' => $ts->getWorkedHours(),
                    'description' => $ts->description
                ];
            }),
            'total_hours' => round($totalHours, 2)
        ]);
    }
}
