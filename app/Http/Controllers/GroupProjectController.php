<?php

namespace App\Http\Controllers;

use App\Models\GroupProject;
use Illuminate\Http\Request;

class GroupProjectController extends Controller
{
    public function index()
    {
        $groupProjects = GroupProject::withCount('projects')->get();
        return view('admin.group-projects.index', compact('groupProjects'));
    }

    public function create()
    {
        return view('admin.group-projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:group_projects',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:active,inactive,planned,completed',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        
        // Set default color if not provided
        if (empty($data['color'])) {
            $data['color'] = $this->getRandomColor();
        }
        
        // Set default icon if not provided
        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-folder';
        }

        GroupProject::create($data);

        return redirect()->route('admin.group-projects.index')->with('success', 'Group Project created successfully.');
    }

    public function show(GroupProject $groupProject)
    {
        $groupProject->load(['projects.users', 'projects.timesheets']);
        return view('admin.group-projects.show', compact('groupProject'));
    }

    public function edit(GroupProject $groupProject)
    {
        return view('admin.group-projects.edit', compact('groupProject'));
    }

    public function update(Request $request, GroupProject $groupProject)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:group_projects,name,' . $groupProject->getKey(),
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:active,inactive,planned,completed',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        
        // Set default color if not provided
        if (empty($data['color'])) {
            $data['color'] = $this->getRandomColor();
        }
        
        // Set default icon if not provided
        if (empty($data['icon'])) {
            $data['icon'] = 'fas fa-folder';
        }

        $groupProject->update($data);

        return redirect()->route('admin.group-projects.index')->with('success', 'Group Project updated successfully.');
    }

    public function destroy(GroupProject $groupProject)
    {
        // Check if group project has projects
        if ($groupProject->projects()->count() > 0) {
            return redirect()->route('admin.group-projects')
                ->with('error', 'Cannot delete group project that contains projects.');
        }

        $groupProject->delete();

        return redirect()->route('admin.group-projects.index')->with('success', 'Group Project deleted successfully.');
    }

    /**
     * Generate a random color for the group project
     */
    private function getRandomColor()
    {
        $colors = [
            '#007bff', '#6f42c1', '#e83e8c', '#dc3545', '#fd7e14',
            '#ffc107', '#28a745', '#20c997', '#17a2b8', '#6c757d'
        ];
        
        return $colors[array_rand($colors)];
    }
}
