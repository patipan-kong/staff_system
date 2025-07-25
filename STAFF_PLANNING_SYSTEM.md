# Staff Planning System

This document describes the staff planning feature that allows managers and admins to schedule staff assignments to projects on a weekly calendar basis.

## Overview

The Staff Planning system provides a visual weekly calendar interface where managers can:
- View all staff members in a table format with days of the week as columns
- Assign staff to specific projects on specific dates by clicking on table cells
- View multiple project assignments per person per day
- Remove assignments by clicking on project badges
- Navigate between weeks
- Filter view by specific projects

## Features

### 1. Weekly Calendar View
- **Layout**: Table format with staff members as rows and weekdays (Mon-Fri) as columns
- **Sticky Column**: Staff member column remains visible when scrolling horizontally
- **Week Navigation**: Previous/Next week buttons and "Current Week" quick access
- **Responsive Design**: Works on desktop and mobile devices

### 2. Staff Assignment
- **Click to Assign**: Click any cell to open project selection modal
- **Project Selection**: Modal shows all active/planning projects with visual indicators
- **Multiple Assignments**: Staff can be assigned to multiple projects on the same day
- **Visual Indicators**: Projects display with custom colors and icons

### 3. Project Management
- **Project Colors**: Each assignment shows in the project's custom color
- **Project Icons**: FontAwesome icons display alongside project names
- **Project Status**: Only active and planning projects are available for assignment
- **Project Groups**: Projects are organized by their group associations

### 4. Assignment Management
- **Easy Removal**: Click the 'X' button on any assignment to remove it
- **Duplicate Prevention**: System prevents duplicate assignments (same user, project, date)
- **Real-time Updates**: Changes are immediately reflected in the interface
- **Confirmation**: Deletion requires user confirmation

## Implementation Details

### Database Schema

**Staff Plannings Table** (`staff_plannings`):
```php
- id (Primary Key)
- user_id (Foreign Key to users)
- project_id (Foreign Key to projects)  
- planned_date (Date)
- created_by (Foreign Key to users - who created the assignment)
- notes (Text, nullable)
- timestamps
- Unique constraint on (user_id, project_id, planned_date)
```

### Backend Components

**StaffPlanningController**:
- `index()`: Display weekly calendar with existing assignments
- `store()`: Create new staff assignment (AJAX endpoint)
- `destroy()`: Remove staff assignment (AJAX endpoint)

**StaffPlanning Model**:
- Relationships to User, Project, and Creator
- Date casting for planned_date field
- Fillable attributes for mass assignment protection

### Frontend Components

**Weekly Calendar Interface**:
- Bootstrap table with sticky first column
- Color-coded project assignments
- Interactive cells for adding assignments
- Hover effects and smooth transitions

**Project Selection Modal**:
- Lists all available projects
- Shows project colors, icons, and status
- Group information display
- Click-to-select functionality

**AJAX Operations**:
- Assignment creation without page reload
- Assignment deletion with confirmation
- Error handling and user feedback
- CSRF token protection

### User Experience Features

**Visual Design**:
- Project assignments display as colored badges
- Icons provide quick project type identification
- Hover effects for interactive elements
- Responsive layout adapts to screen size

**Navigation**:
- Week-by-week navigation
- Quick return to current week
- Smooth transitions between weeks

**Interaction**:
- Click cells to assign projects
- Click project badges to remove assignments
- Project filter dropdown (future enhancement)
- Confirmation dialogs for destructive actions

## Access Control

**Manager/Admin Only**: 
- Route protected by `manager_or_admin` middleware
- Only users with role level 2+ can access
- Assignment creation tracks who made the assignment

**Permission Levels**:
- **Staff (Role 0-1)**: No access to staff planning
- **Manager (Role 2)**: Full access to staff planning
- **Admin (Role 99)**: Full access to staff planning

## Navigation Integration

**Menu Location**: Management dropdown in main navigation
- Accessible to managers and admins
- Icon: `fas fa-calendar-alt`
- Label: "Staff Planning"

## API Endpoints

### GET /staff-planning
- **Purpose**: Display weekly planning calendar
- **Parameters**: `week` (optional) - Date string for week start
- **Returns**: HTML view with staff, projects, and assignments

### POST /staff-planning  
- **Purpose**: Create new staff assignment
- **Parameters**: 
  - `user_id`: ID of staff member
  - `project_id`: ID of project
  - `planned_date`: Date for assignment
- **Returns**: JSON with assignment details or error

### DELETE /staff-planning/{planning}
- **Purpose**: Remove staff assignment
- **Parameters**: Planning ID in URL
- **Returns**: JSON success confirmation

## Data Flow

1. **Page Load**: 
   - Get current/specified week dates
   - Load all users and active projects
   - Fetch existing assignments for the week
   - Group assignments by user and date

2. **Assignment Creation**:
   - User clicks on cell → Modal opens
   - User selects project → AJAX POST request
   - Server validates and creates assignment
   - Client updates UI with new assignment badge

3. **Assignment Deletion**:
   - User clicks delete button → Confirmation dialog
   - User confirms → AJAX DELETE request
   - Server removes assignment
   - Client removes assignment badge from UI

## Error Handling

**Client-Side**:
- Network error alerts
- Duplicate assignment warnings
- Form validation feedback

**Server-Side**:
- Duplicate assignment prevention
- Foreign key validation
- Authentication/authorization checks
- JSON error responses

## Future Enhancements

1. **Bulk Operations**: Select multiple cells for batch assignment
2. **Drag & Drop**: Drag projects onto calendar cells
3. **Time Slots**: Assign specific hours within days
4. **Conflict Detection**: Warn about scheduling conflicts
5. **Template Weeks**: Save and reuse planning patterns
6. **Export Features**: PDF/Excel export of weekly plans
7. **Integration**: Link with timesheet system for actual vs planned
8. **Notifications**: Alert staff of schedule changes
9. **Color Coding**: Different colors for different assignment types
10. **Advanced Filtering**: Filter by department, project type, etc.

## Technical Notes

- Uses Bootstrap 5 for responsive design
- Vanilla JavaScript for interactions (no additional frameworks)
- Laravel's built-in CSRF protection
- Eloquent ORM for database operations
- Font Awesome icons for visual elements
- CSS Grid/Flexbox for layout
- AJAX for seamless user experience

The Staff Planning system provides managers with a powerful yet intuitive tool for organizing team schedules and ensuring optimal project resource allocation.
