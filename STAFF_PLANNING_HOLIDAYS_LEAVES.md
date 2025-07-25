# Staff Planning - Holiday and Leave Display Features

## Overview
The Staff Planning page has been enhanced to visually display company holidays and staff leaves directly in the weekly calendar view, providing better visibility for managers when planning staff assignments.

## Visual Indicators

### Company Holidays
- **Header Styling**: Holiday dates are highlighted with a red background tint and red top border
- **Holiday Icon**: A red calendar-times icon appears in the header with a tooltip showing the holiday name
- **Holiday Name**: The holiday name is displayed below the date in the header
- **Cell Styling**: All cells for a holiday date have a light red background and red left border

### Staff Leaves
- **Cell Styling**: Individual cells for staff members on leave have a light yellow/orange background and yellow left border
- **Leave Icon**: A yellow user-times icon is displayed in the center of the cell with the leave type below it
- **Leave Types**: Displays the type of leave (vacation, sick, personal, etc.)

### Project Assignments
- **Dimmed Projects**: Projects assigned on holidays or leave days are displayed with reduced opacity (70%) to indicate they may need review
- **Assignment Warning**: When attempting to assign projects on holidays or leave days, a confirmation dialog appears

## Legend
A legend is displayed below the calendar showing:
- Company Holiday indicator (red background with border)
- Staff Leave indicator (yellow background with border)  
- Holiday icon explanation
- Leave icon explanation
- Helpful tip about dimmed project assignments

## Interactive Features

### Assignment Warnings
- When clicking on a holiday cell: "This is a company holiday. Are you sure you want to assign a project on this day?"
- When clicking on a leave cell: "This is a staff leave day. Are you sure you want to assign a project on this day?"

### Visual Feedback
- Hover effects on legend items
- Tooltip support for holiday icons showing holiday names
- Smooth transitions for visual elements

## Data Sources

### Company Holidays
- Loaded from the `company_holidays` table
- Filtered to show only holidays within the current week (Monday-Friday)
- Supports holiday names, dates, and descriptions

### Staff Leaves
- Loaded from the `leaves` table with approved status
- Filtered to show leaves that overlap with the current week
- Supports different leave types (vacation, sick, personal, etc.)
- Shows leaves that start before, end after, or fall within the week

## Implementation Details

### Backend (StaffPlanningController)
- `$holidays`: Collection of company holidays keyed by date
- `$leaves`: Collection of staff leaves grouped by user_id
- Date range filtering for current week (Monday-Friday)

### Frontend (staff-planning/index.blade.php)
- PHP logic to check holiday/leave status for each cell
- CSS styling for visual indicators
- JavaScript for assignment warnings and tooltips
- Responsive legend with color-coded indicators

### Styling
- Bootstrap-based responsive design
- Custom CSS for holiday/leave indicators
- Smooth transitions and hover effects
- Color-coded visual hierarchy

## Benefits
1. **Better Planning**: Managers can immediately see when staff are unavailable
2. **Conflict Prevention**: Visual warnings prevent accidental assignments on holidays/leaves
3. **Clear Communication**: Legend and tooltips provide clear meaning for all indicators
4. **Professional UI**: Clean, color-coded design that's easy to understand at a glance
5. **Accessibility**: Tooltips and clear visual indicators support accessibility requirements
