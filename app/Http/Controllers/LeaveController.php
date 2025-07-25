<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isManagerOrAdmin()) {
            $leaves = Leave::with(['user', 'approvedBy'])->latest()->paginate(20);
        } else {
            $leaves = $user->leaves()->with('approvedBy')->latest()->paginate(20);
        }

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:vacation,sick,sick_with_certificate,personal',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['status'] = Leave::STATUS_PENDING;

        $leave = new Leave($data);
        $leave->days = $leave->calculateDays();

        if ($request->hasFile('medical_certificate')) {
            $data['medical_certificate'] = $request->file('medical_certificate')
                ->store('medical_certificates', 'public');
        }

        $leave->fill($data)->save();

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(Leave $leave)
    {
        $this->authorize('view', $leave);
        
        // Calculate leave summary for the user in current year
        $currentYear = now()->year;
        $leaveSummary = $this->calculateLeaveSummary($leave->user, $currentYear);
        
        return view('leaves.show', compact('leave', 'leaveSummary', 'currentYear'));
    }

    public function edit(Leave $leave)
    {
        $this->authorize('update', $leave);
        return view('leaves.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorize('update', $leave);

        $request->validate([
            'type' => 'required|in:vacation,sick,sick_with_certificate,personal',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $leave->fill($data);
        $leave->days = $leave->calculateDays();

        if ($request->hasFile('medical_certificate')) {
            if ($leave->medical_certificate) {
                Storage::disk('public')->delete($leave->medical_certificate);
            }
            $data['medical_certificate'] = $request->file('medical_certificate')
                ->store('medical_certificates', 'public');
        }

        $leave->fill($data)->save();

        return redirect()->route('leaves.show', $leave)->with('success', 'Leave request updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);
        
        if ($leave->medical_certificate) {
            Storage::disk('public')->delete($leave->medical_certificate);
        }
        
        $leave->delete();

        return redirect()->route('leaves.index')->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Request $request, Leave $leave)
    {
        $this->authorize('approve', $leave);

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $this->authorize('approve', $leave);

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => Leave::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    /**
     * Download medical certificate for a leave request
     */
    public function downloadMedicalCertificate(Leave $leave)
    {
        $this->authorize('downloadMedicalCertificate', $leave);

        if (!$leave->medical_certificate) {
            abort(404, 'Medical certificate not found.');
        }

        if (!Storage::disk('public')->exists($leave->medical_certificate)) {
            abort(404, 'Medical certificate file not found.');
        }

        $filePath = storage_path('app/public/' . $leave->medical_certificate);
        $fileName = 'medical_certificate_' . $leave->id . '_' . basename($leave->medical_certificate);

        return response()->download($filePath, $fileName);
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
}