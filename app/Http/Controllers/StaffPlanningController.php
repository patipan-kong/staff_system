<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\StaffPlanning;
use App\Models\CompanyHoliday;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffPlanningController extends Controller
{
    public function index(Request $request)
    {
        // Get the week starting date (Monday)
        $weekStart = $request->get('week') 
            ? Carbon::parse($request->get('week'))->startOfWeek() 
            : Carbon::now()->startOfWeek();
        
        // Generate week dates (Monday to Friday)
        $weekDates = [];
        for ($i = 0; $i < 5; $i++) {
            $weekDates[] = $weekStart->copy()->addDays($i);
        }
        
        // Get all active users
        $users = User::orderBy('name')->get();
        
        // Get all active projects
        $projects = Project::whereIn('status', ['planning', 'active'])
            ->orderBy('name')
            ->get();
        
        // Get existing staff planning for the week
        $staffPlannings = StaffPlanning::with(['user', 'project'])
            ->whereBetween('planned_date', [
                $weekStart->format('Y-m-d'),
                $weekStart->copy()->addDays(4)->format('Y-m-d')
            ])
            ->get()
            ->groupBy(function ($item) {
                return $item->user_id . '_' . $item->planned_date->format('Y-m-d');
            });
        
        // Get company holidays for the week
        $holidays = CompanyHoliday::whereBetween('date', [
                $weekStart->format('Y-m-d'),
                $weekStart->copy()->addDays(4)->format('Y-m-d')
            ])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });
        
        // Get staff leaves for the week
        $leaves = Leave::with('user')
            ->where('status', 'approved')
            ->where(function ($query) use ($weekStart) {
                $weekEnd = $weekStart->copy()->addDays(4);
                $query->whereBetween('start_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                      ->orWhere(function ($subQuery) use ($weekStart, $weekEnd) {
                          $subQuery->where('start_date', '<=', $weekStart->format('Y-m-d'))
                                   ->where('end_date', '>=', $weekEnd->format('Y-m-d'));
                      });
            })
            ->get()
            ->groupBy('user_id');
        
        return view('staff-planning.index', compact('users', 'projects', 'weekDates', 'weekStart', 'staffPlannings', 'holidays', 'leaves'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'planned_date' => 'required|date',
        ]);
        
        // Check if this planning already exists
        $existing = StaffPlanning::where('user_id', $request->get('user_id'))
            ->where('project_id', $request->get('project_id'))
            ->where('planned_date', $request->get('planned_date'))
            ->first();
            
        if ($existing) {
            return response()->json(['error' => 'This planning already exists'], 400);
        }
        
        $planning = StaffPlanning::create([
            'user_id' => $request->get('user_id'),
            'project_id' => $request->get('project_id'),
            'planned_date' => $request->get('planned_date'),
            'created_by' => auth()->id(),
        ]);
        
        $planning->load('project');
        
        return response()->json([
            'id' => $planning->id,
            'project_name' => $planning->project->name,
            'project_color' => $planning->project->color ?? '#007bff',
            'project_icon' => $planning->project->icon,
        ]);
    }
    
    public function destroy($id)
    {
        $planning = StaffPlanning::findOrFail($id);
        $planning->delete();
        
        return response()->json(['success' => true]);
    }
}
