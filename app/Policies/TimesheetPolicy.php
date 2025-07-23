<?php

namespace App\Policies;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimesheetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isStaff(); // All staff can view timesheets (their own)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timesheet $timesheet)
    {
        // Users can view their own timesheets, managers can view all
        return $user->id === $timesheet->user_id || $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timesheet $timesheet)
    {
        // Users can only update their own timesheets
        return $user->id === $timesheet->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timesheet $timesheet)
    {
        // Users can only delete their own timesheets
        return $user->id === $timesheet->user_id;
    }
}
