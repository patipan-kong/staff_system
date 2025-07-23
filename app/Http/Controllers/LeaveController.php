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
        return view('leaves.show', compact('leave'));
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
}