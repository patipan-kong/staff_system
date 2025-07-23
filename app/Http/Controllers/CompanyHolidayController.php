<?php

namespace App\Http\Controllers;

use App\Models\CompanyHoliday;
use Illuminate\Http\Request;

class CompanyHolidayController extends Controller
{
    public function index()
    {
        $holidays = CompanyHoliday::orderBy('date')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_recurring'] = $request->has('is_recurring');

        CompanyHoliday::create($data);

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday created successfully.');
    }

    public function show(CompanyHoliday $holiday)
    {
        return view('admin.holidays.show', compact('holiday'));
    }

    public function edit(CompanyHoliday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, CompanyHoliday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_recurring'] = $request->has('is_recurring');

        $holiday->update($data);

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy(CompanyHoliday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
