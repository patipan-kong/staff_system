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
            'nickname' => 'nullable|string|max:100',
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
            'vacation_quota' => 'nullable|integer|min:0|max:365',
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
        
        $user->load(['department', 'projects', 'timesheets.project', 'leaves']);
        
        // Calculate leave summaries for current year
        $currentYear = now()->year;
        $leaveSummary = $this->calculateLeaveSummary($user, $currentYear);
        
        return view('users.show', compact('user', 'leaveSummary', 'currentYear'));
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
            'nickname' => 'nullable|string|max:100',
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
            'vacation_quota' => 'nullable|integer|min:0|max:365',
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
        
        // Calculate leave summaries for current year
        $currentYear = now()->year;
        $leaveSummary = $this->calculateLeaveSummary($user, $currentYear);
        
        return view('users.profile', compact('user', 'leaveSummary', 'currentYear'));
    }

    /**
     * Calculate leave summary for a user in a given year
     */
    private function calculateLeaveSummary($user, $year)
    {
        $leaves = $user->leaves()->whereYear('start_date', $year)->get();
        
        $summary = [
            'vacation' => [
                'quota' => $user->vacation_quota ?? 0,
                'approved' => 0,
                'pending' => 0,
                'remain' => 0
            ],
            'personal' => [
                'quota' => $user->vacation_quota ?? 0, // Using same quota as vacation
                'approved' => 0,
                'pending' => 0,
                'remain' => 0
            ],
            'sick' => [
                'quota' => 6, // 6 days for regular sick leave
                'approved' => 0,
                'pending' => 0,
                'remain' => 0
            ],
            'sick_with_certificate' => [
                'quota' => 30, // 30 days for sick leave with certificate
                'approved' => 0,
                'pending' => 0,
                'remain' => 0
            ]
        ];
        
        // Calculate used days for each type
        foreach ($leaves as $leave) {
            $type = $leave->type;
            if (isset($summary[$type])) {
                if ($leave->status === 'approved') {
                    $summary[$type]['approved'] += $leave->days;
                } elseif ($leave->status === 'pending') {
                    $summary[$type]['pending'] += $leave->days;
                }
            }
        }
        
        // For vacation and personal, calculate combined usage against shared quota
        $combinedQuota = $user->vacation_quota ?? 0;
        $vacationUsed = $summary['vacation']['approved'] + $summary['vacation']['pending'];
        $personalUsed = $summary['personal']['approved'] + $summary['personal']['pending'];
        $totalUsed = $vacationUsed + $personalUsed;
        
        // Update remaining days for vacation and personal (shared quota)
        $totalRemaining = max(0, $combinedQuota - $totalUsed);
        $summary['vacation']['remain'] = $totalRemaining;
        $summary['personal']['remain'] = $totalRemaining;
        
        // Calculate remaining days for sick leaves (separate quotas)
        $summary['sick']['remain'] = max(0, $summary['sick']['quota'] - $summary['sick']['approved'] - $summary['sick']['pending']);
        $summary['sick_with_certificate']['remain'] = max(0, $summary['sick_with_certificate']['quota'] - $summary['sick_with_certificate']['approved'] - $summary['sick_with_certificate']['pending']);
        
        return $summary;
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
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