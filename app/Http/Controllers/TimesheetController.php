<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);
        
        $timesheets = $user->timesheets()
            ->with('project')
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function ($timesheet) {
                return $timesheet->date->format('Y-m-d');
            });

        $monthlyHours = $timesheets->flatten()->sum(function ($timesheet) {
            return $timesheet->getWorkedHours();
        });

        return view('timesheets.index', compact('timesheets', 'month', 'monthlyHours'));
    }

    public function create()
    {
        $projects = auth()->user()->projects;
        return view('timesheets.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_minutes' => 'nullable|integer|min:0',
            'description' => 'required|string|max:1000',
            'attachment' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['start_time'] = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->start_time);
        $data['end_time'] = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->end_time);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('timesheet_attachments', 'public');
        }

        Timesheet::create($data);

        return redirect()->route('timesheets.index')->with('success', 'Timesheet entry created successfully.');
    }

    public function show(Timesheet $timesheet)
    {
        $this->authorize('view', $timesheet);
        return view('timesheets.show', compact('timesheet'));
    }

    public function edit(Timesheet $timesheet)
    {
        $this->authorize('update', $timesheet);
        $projects = auth()->user()->projects;
        return view('timesheets.edit', compact('timesheet', 'projects'));
    }

    public function update(Request $request, Timesheet $timesheet)
    {
        $this->authorize('update', $timesheet);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_minutes' => 'nullable|integer|min:0',
            'description' => 'required|string|max:1000',
            'attachment' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['start_time'] = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->start_time);
        $data['end_time'] = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->end_time);

        if ($request->hasFile('attachment')) {
            if ($timesheet->attachment) {
                Storage::disk('public')->delete($timesheet->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('timesheet_attachments', 'public');
        }

        $timesheet->update($data);

        return redirect()->route('timesheets.index')->with('success', 'Timesheet entry updated successfully.');
    }

    public function destroy(Timesheet $timesheet)
    {
        $this->authorize('delete', $timesheet);
        
        if ($timesheet->attachment) {
            Storage::disk('public')->delete($timesheet->attachment);
        }
        
        $timesheet->delete();

        return redirect()->route('timesheets.index')->with('success', 'Timesheet entry deleted successfully.');
    }
}