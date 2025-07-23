<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['manager', 'users'])->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $managers = User::where('role', '>=', 2)->get(); // Managers and above
        return view('admin.departments.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['manager', 'users']);
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $managers = User::where('role', '>=', 2)->get(); // Managers and above
        return view('admin.departments.edit', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->getKey(),
            'description' => 'nullable|string|max:1000',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        // Check if department has users
        if ($department->users()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department that has assigned users.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully.');
    }
}
