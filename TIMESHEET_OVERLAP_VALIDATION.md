# Timesheet Overlap Validation

This document describes the timesheet overlap validation feature that has been implemented in the staff management system.

## Overview

The system now prevents users from creating overlapping timesheet entries for the same day. This ensures accurate time tracking and prevents double-counting of work hours.

## Implementation Details

### 1. Model Methods

The `Timesheet` model includes two static methods for overlap validation:

#### `hasOverlap($userId, $date, $startTime, $endTime, $excludeId = null)`
- **Purpose**: Check if a timesheet entry would overlap with existing entries
- **Parameters**:
  - `$userId`: The user ID to check against
  - `$date`: The date of the timesheet entry  
  - `$startTime`: Start time as Carbon datetime object
  - `$endTime`: End time as Carbon datetime object
  - `$excludeId`: Optional ID to exclude from check (used when updating)
- **Returns**: Boolean (true if overlap exists, false otherwise)

#### `getOverlappingTimesheets($userId, $date, $startTime, $endTime, $excludeId = null)`
- **Purpose**: Retrieve specific timesheet entries that overlap
- **Parameters**: Same as `hasOverlap`
- **Returns**: Collection of overlapping Timesheet models with project relationships loaded

### 2. Controller Integration

The validation has been integrated into `TimesheetController`:

#### Store Method (`POST /timesheets`)
- Validates basic form data first
- Converts time inputs to Carbon datetime objects
- Checks for overlaps using `hasOverlap()` method
- If overlap detected:
  - Retrieves overlapping entries details
  - Returns to form with user-friendly error message
  - Preserves form input for user convenience
- If no overlap, proceeds with creating timesheet

#### Update Method (`PUT /timesheets/{timesheet}`)
- Similar validation as store method
- Uses `excludeId` parameter to ignore the current timesheet being updated
- Allows updating a timesheet to non-overlapping times

### 3. User Interface

Error messages are displayed prominently in both create and edit forms:

- **Alert Style**: Bootstrap danger alert with dismiss button
- **Error Content**: Lists specific overlapping entries with project names and time ranges
- **Example**: "This timesheet overlaps with existing entries: Project Alpha (09:00 - 12:00), Project Beta (14:00 - 17:00). Please adjust your start or end time."

### 4. Overlap Detection Logic

The system detects overlaps using the following logic:
```
New entry overlaps if:
  new_start_time < existing_end_time AND new_end_time > existing_start_time
```

This correctly identifies:
- **Complete overlaps**: New entry completely within existing entry
- **Partial overlaps**: New entry starts before or ends after existing entry
- **Surrounding overlaps**: New entry completely encompasses existing entry
- **Adjacent entries**: Entries that touch but don't overlap are allowed

### 5. Example Scenarios

#### Scenario 1: Overlap Detected
- Existing: 09:00 - 12:00
- New: 10:00 - 14:00
- **Result**: ❌ Overlap detected (10:00-12:00)

#### Scenario 2: No Overlap
- Existing: 09:00 - 12:00  
- New: 13:00 - 16:00
- **Result**: ✅ No overlap, allowed

#### Scenario 3: Adjacent Times
- Existing: 09:00 - 12:00
- New: 12:00 - 15:00
- **Result**: ✅ No overlap, allowed (times touch but don't overlap)

## Benefits

1. **Data Integrity**: Prevents double-counting of work hours
2. **User Experience**: Clear error messages help users correct timing conflicts
3. **Audit Trail**: Maintains accurate timesheet records for reporting
4. **Flexibility**: Allows updates and adjacent time entries

## Future Enhancements

1. **Visual Calendar**: Show existing entries when creating new timesheets
2. **Auto-suggestion**: Suggest available time slots
3. **Bulk Operations**: Handle overlaps when importing timesheet data
4. **Team Coordination**: Prevent resource conflicts across team members

## Testing

Comprehensive tests have been created to validate:
- Overlap detection accuracy
- Form validation integration  
- Error message display
- Update functionality with exclusions

The validation is now active for all timesheet create and update operations.
