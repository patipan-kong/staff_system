# User Profile - Leave Summary Feature

## Overview
The user profile pages now display a comprehensive yearly leave summary showing quota, used days (approved and pending), and remaining days for each leave type in separate, color-coded boxes.

## Leave Summary Display

### User Profile Pages
Each leave type is displayed in its own card with:
- **Header**: Icon, leave type name, and color-coded theme
- **Quota**: Total annual allowance for that leave type
- **Used (Approved)**: Days already approved and taken
- **Used (Pending)**: Days requested but awaiting approval
- **Remain**: Days still available to use
- **Progress Bar**: Visual representation of quota usage percentage

### Leave Detail Pages
On individual leave request pages, a summary box shows:
- **Focused View**: Summary for the specific leave type being viewed
- **Same Format**: Quota, Used (Approved), Used (Pending), Remain
- **Visual Progress**: Progress bar showing usage percentage
- **Context**: Relevant notes about shared quotas for vacation/personal leave

### Leave Types and Quotas

#### 1. Vacation Leave
- **Quota**: Uses `vacation_quota` from user model
- **Color**: Blue (Primary)
- **Icon**: Umbrella beach
- **Shared Quota**: Combined with Personal leave

#### 2. Personal Leave
- **Quota**: Uses `vacation_quota` from user model (shared with vacation)
- **Color**: Light Blue (Info)
- **Icon**: User clock
- **Shared Quota**: Combined with Vacation leave

#### 3. Sick Leave
- **Quota**: Fixed at 6 days per year
- **Color**: Yellow (Warning)
- **Icon**: Thermometer
- **Independent Quota**: Separate from other leave types

#### 4. Sick Leave with Certificate
- **Quota**: Fixed at 30 days per year
- **Color**: Red (Danger)
- **Icon**: Medical file
- **Independent Quota**: Separate from other leave types

## Display Format
Each leave type card shows information in this exact format:
```
Quota: [X] days
Used (Approved): [X.X] days
Used (Pending): [X.X] days
Remain: [X.X] days
[Progress bar showing usage percentage]
```

## Quota Logic

### Shared Quota (Vacation + Personal)
- Vacation and Personal leave types share the same quota from `user.vacation_quota`
- The system calculates total usage across both types
- Remaining days shown for both represents the shared remaining balance
- Example: If quota is 15 days, user takes 5 vacation + 3 personal = 7 remaining for both types

### Independent Quotas (Sick Leaves)
- Regular sick leave: 6 days per year (independent)
- Sick leave with certificate: 30 days per year (independent)
- These don't affect vacation/personal quotas

## Implementation Details

### Backend (UserController & LeaveController)
- `calculateLeaveSummary()` method processes leave data for current year (both controllers)
- UserController: Handles profile and user detail views
- LeaveController: Handles individual leave request detail views
- Handles shared quota logic for vacation/personal leave
- Separates approved vs pending leave calculations
- Returns structured array with all leave type summaries

### Data Processing
```php
$leaveSummary = [
    'vacation' => [
        'quota' => $user->vacation_quota,
        'approved' => $approvedDays,
        'pending' => $pendingDays,
        'remain' => $remainingDays
    ],
    // ... other leave types
];
```

### Frontend Display
- Responsive grid layout (4 cards per row on large screens)
- Color-coded cards matching leave type themes
- Progress bars with percentage calculations
- Bootstrap badge styling for values
- Informational alert explaining shared quotas

### Views Updated
- `resources/views/users/profile.blade.php` - User's own profile
- `resources/views/users/show.blade.php` - Viewing other users (managers/admins)
- `resources/views/leaves/show.blade.php` - Individual leave request details

## Access Control
- **Own Profile**: Users can see their own leave summary
- **Manager/Admin View**: Managers and admins can see all users' leave summaries
- **Regular Staff**: Cannot see other users' leave details

## Visual Features

### Color Coding
- **Vacation**: Blue theme for leisure/planned time off
- **Personal**: Light blue for personal matters
- **Sick**: Yellow/warning for health-related absence
- **Sick w/ Certificate**: Red for serious medical conditions

### Progress Indicators
- Visual progress bars show quota utilization
- Percentage calculations help with planning
- Color-matched to leave type theme

### Responsive Design
- Mobile-friendly card layout
- Stacks vertically on small screens
- Maintains readability across devices

## Benefits
1. **Clear Overview**: Instant visibility of all leave balances
2. **Planning Support**: Helps users plan future leave requests
3. **Manager Insight**: Allows managers to see team availability
4. **Quota Awareness**: Users understand shared vs independent quotas
5. **Status Clarity**: Distinguishes between approved and pending requests
6. **Visual Appeal**: Professional, color-coded presentation

## Usage Examples

### User Planning
A user can quickly see:
- "I have 8 vacation days remaining"
- "I have 2 pending personal days affecting my quota"
- "I have 6 sick days available if needed"

### Manager Review
A manager can assess:
- Team member availability for projects
- Who might need leave quota adjustments
- Pending leave requests impact on quotas

## Technical Notes
- Leave calculations based on current calendar year
- Handles partial days (0.5, 1.5, etc.)
- Progress bars max out at 100% even if over-quota
- Null vacation quotas default to 0
- Year context clearly displayed in section header
