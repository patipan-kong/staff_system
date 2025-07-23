# LeavePolicy Test Documentation

## Testing the LeavePolicy Authorization

The LeavePolicy has been implemented with the following authorization rules:

### 1. **View Permissions**
- **viewAny()**: All authenticated users can access the leaves index
- **view()**: Users can view their own leaves; Managers/Admins can view any leave

### 2. **Create Permissions**
- **create()**: All authenticated users can create leave requests

### 3. **Update Permissions**
- **update()**: 
  - Users can only update their own PENDING leaves
  - Admins can update any leave

### 4. **Delete Permissions**
- **delete()**:
  - Users can only delete their own PENDING leaves
  - Admins can delete any leave

### 5. **Approval Permissions**
- **approve()**: Only Managers/Admins can approve leaves (not their own)
- **reject()**: Only Managers/Admins can reject leaves (not their own)

### 6. **File Access Permissions**
- **downloadMedicalCertificate()**: Users can download their own certificates; Managers/Admins can download any

### 7. **Statistics Permissions**
- **viewStatistics()**: Only Managers/Admins can view leave statistics

## Usage in Controllers

The policy is automatically applied in LeaveController methods using:
```php
$this->authorize('view', $leave);
$this->authorize('update', $leave);
$this->authorize('delete', $leave);
$this->authorize('approve', $leave);
$this->authorize('downloadMedicalCertificate', $leave);
```

## Policy Registration

The policy is registered in `app/Providers/AuthServiceProvider.php`:
```php
protected $policies = [
    'App\Models\Leave' => 'App\Policies\LeavePolicy',
];
```

## Status Constants Used

The policy uses Leave model constants:
- `Leave::STATUS_PENDING` - For allowing edits/deletes
- Leave status checking prevents modification of approved/rejected leaves

## Security Features

1. **Ownership Checking**: Users can only modify their own leaves
2. **Status Validation**: Only pending leaves can be modified by users
3. **Role-Based Access**: Managers/Admins have elevated permissions
4. **Self-Approval Prevention**: Users cannot approve their own leave requests
5. **File Security**: Medical certificate downloads are properly authorized
