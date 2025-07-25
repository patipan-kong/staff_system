<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\GroupProject;
use App\Models\User;
use App\Models\ProjectAssign;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isManagerOrAdmin()) {
            $projects = Project::with(['groupProject', 'users', 'timesheets'])->get();
        } else {
            $projects = $user->projects()->with(['groupProject', 'timesheets'])->get();
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        
        $groupProjects = GroupProject::all();
        $users = User::all();
        
        return view('projects.create', compact('groupProjects', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_project_id' => 'required|exists:group_projects,id',
            'estimated_hours' => 'nullable|numeric|min:0',
            'budget' => 'nullable|numeric|min:0',
            'po_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:po_date',
            'on_production_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:planning,active,on_hold,completed',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'assigned_users' => 'array',
            'assigned_users.*' => 'exists:users,id',
            'user_estimated_hours' => 'array',
            'user_estimated_hours.*' => 'nullable|numeric|min:0',
        ]);

        $project = Project::create($request->except(['assigned_users', 'user_estimated_hours']));

        if ($request->has('assigned_users')) {
            foreach ($request->assigned_users as $userId) {
                $estimatedHours = $request->user_estimated_hours[$userId] ?? null;
                ProjectAssign::create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'assigned_date' => now(),
                    'estimated_hours' => $estimatedHours,
                ]);
            }
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load(['groupProject', 'users', 'timesheets.user']);
        
        $totalHours = $project->timesheets->sum(function ($timesheet) {
            return $timesheet->getWorkedHours();
        });
        
        $totalCost = $project->getTotalCost();
        $progress = $project->getProgress();
        
        return view('projects.show', compact('project', 'totalHours', 'totalCost', 'progress'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        
        $groupProjects = GroupProject::all();
        $users = User::all();
        $assignedUsers = $project->users->pluck('id')->toArray();
        
        // Get estimated hours for each assigned user
        $userEstimatedHours = [];
        $projectAssigns = $project->projectAssigns()->with('user')->get();
        foreach ($projectAssigns as $assign) {
            $userEstimatedHours[$assign->user_id] = $assign->estimated_hours;
        }
        
        return view('projects.edit', compact('project', 'groupProjects', 'users', 'assignedUsers', 'userEstimatedHours'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_project_id' => 'required|exists:group_projects,id',
            'estimated_hours' => 'nullable|numeric|min:0',
            'budget' => 'nullable|numeric|min:0',
            'po_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:po_date',
            'on_production_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:planning,active,on_hold,completed',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'assigned_users' => 'array',
            'assigned_users.*' => 'exists:users,id',
            'user_estimated_hours' => 'array',
            'user_estimated_hours.*' => 'nullable|numeric|min:0',
        ]);

        $project->update($request->except(['assigned_users', 'user_estimated_hours']));

        // Update project assignments
        ProjectAssign::where('project_id', $project->id)->delete();
        
        if ($request->has('assigned_users')) {
            foreach ($request->assigned_users as $userId) {
                $estimatedHours = $request->user_estimated_hours[$userId] ?? null;
                ProjectAssign::create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'assigned_date' => now(),
                    'estimated_hours' => $estimatedHours,
                ]);
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}