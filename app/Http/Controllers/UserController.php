<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with('department')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        
        $departments = Department::all();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:0,1,2,3,99',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load('department');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        $departments = Department::all();
        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|integer|in:0,1,2,3,99',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        $user->update($data);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function profile()
    {
        $user = auth()->user();
        $user->load(['department', 'projects', 'timesheets.project', 'leaves']);
        
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}