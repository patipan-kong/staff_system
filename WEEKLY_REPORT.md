# Weekly Distribution Report

## Overview
The Weekly Distribution Report provides a comprehensive view of team activity and time distribution across a weekly calendar format. This report is accessible to all authenticated users and displays real-time data about team member activities, project time allocation, and leave schedules.

## Features

### 📊 Weekly Calendar View
- **Grid Layout**: Shows all team members in rows with days of the week as columns
- **Interactive Cells**: Click on any cell to view detailed timesheet entries for that user/date
- **Visual Indicators**: 
  - Green badges for logged hours
  - Yellow badges for users on leave
  - Gray indicators for no activity
  - Project color-coded badges for quick identification

### 📈 Weekly Statistics Dashboard
- **Total Hours**: Sum of all logged hours for the week
- **Time Entries**: Total number of timesheet entries
- **Active Users**: Count of users who logged time
- **On Leave**: Count of users with approved leave

### 🎯 Project Distribution Analysis
- **Project Time Breakdown**: Shows how time is distributed across different projects
- **Percentage Analysis**: Visual progress bars showing project time allocation
- **Active Projects List**: Current active projects with group information

### 🔄 Navigation & Filters
- **Week Navigation**: Easy previous/next week navigation
- **Week Picker**: Jump to any specific week using HTML5 week input
- **Current Week Button**: Quick return to current week

## Access & Permissions
- **Public Access**: Available to all authenticated users (staff level and above)
- **No Special Permissions**: No admin or manager privileges required
- **Real-time Data**: Shows current data from timesheets and leave records

## URL Structure
- Main Report: `/reports/weekly`
- Week Navigation: `/reports/weekly?week=2025-W30`
- AJAX Data: `/reports/timesheet-data` (for modal details)

## Technical Implementation

### Routes
```php
Route::get('/reports/weekly', [ReportController::class, 'weeklyDistribution'])->name('reports.weekly');
Route::get('/reports/timesheet-data', [ReportController::class, 'getTimesheetData'])->name('reports.timesheet-data');
```

### Key Components
1. **ReportController**: Handles data aggregation and view rendering
2. **Weekly Distribution View**: Responsive calendar grid with Bootstrap 5
3. **AJAX Modal**: Detailed timesheet data on demand
4. **Responsive Design**: Mobile-friendly layout

### Data Sources
- **Timesheets**: User time entries with project associations
- **Leaves**: Approved leave requests
- **Projects**: Active project information with group data
- **Users**: Team member information with departments

## Usage Tips
- **Click any calendar cell** to see detailed timesheet entries
- **Use week navigation** to analyze historical data
- **Check project distribution** to identify time allocation patterns
- **Monitor team activity** to ensure balanced workloads
- **Track leave patterns** for better resource planning

## Navigation Access
The weekly report is accessible from the main navigation bar:
**Navigation → Weekly Report** (visible to all authenticated users)
