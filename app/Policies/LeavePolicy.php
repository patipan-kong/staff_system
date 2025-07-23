<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any leaves.
     */
    public function viewAny(User $user)
    {
        // All authenticated users can view their own leaves
        // Managers and admins can view all leaves
        return true;
    }

    /**
     * Determine whether the user can view the leave.
     */
    public function view(User $user, Leave $leave)
    {
        // Users can view their own leaves
        // Managers and admins can view any leave
        return $user->id === $leave->user_id || $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can create leaves.
     */
    public function create(User $user)
    {
        // All authenticated users can create leave requests
        return true;
    }

    /**
     * Determine whether the user can update the leave.
     */
    public function update(User $user, Leave $leave)
    {
        // Users can only update their own pending leaves
        // Admins can update any leave
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only edit their own leaves if they are pending
        return $user->id === $leave->user_id && $leave->status === Leave::STATUS_PENDING;
    }

    /**
     * Determine whether the user can delete the leave.
     */
    public function delete(User $user, Leave $leave)
    {
        // Users can only delete their own pending leaves
        // Admins can delete any leave
        if ($user->isAdmin()) {
            return true;
        }

        // Users can only delete their own leaves if they are pending
        return $user->id === $leave->user_id && $leave->status === Leave::STATUS_PENDING;
    }

    /**
     * Determine whether the user can approve/reject leaves.
     */
    public function approve(User $user, Leave $leave)
    {
        // Only managers and admins can approve/reject leaves
        // Cannot approve their own leave requests
        return $user->isManagerOrAdmin() && $user->id !== $leave->user_id;
    }

    /**
     * Determine whether the user can reject leaves.
     */
    public function reject(User $user, Leave $leave)
    {
        // Same as approve - only managers and admins can reject leaves
        // Cannot reject their own leave requests
        return $user->isManagerOrAdmin() && $user->id !== $leave->user_id;
    }

    /**
     * Determine whether the user can restore the leave.
     */
    public function restore(User $user, Leave $leave)
    {
        // Only admins can restore deleted leaves
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the leave.
     */
    public function forceDelete(User $user, Leave $leave)
    {
        // Only admins can permanently delete leaves
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can download medical certificates.
     */
    public function downloadMedicalCertificate(User $user, Leave $leave)
    {
        // Users can download their own medical certificates
        // Managers and admins can download any medical certificate
        return $user->id === $leave->user_id || $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can view leave statistics.
     */
    public function viewStatistics(User $user)
    {
        // Only managers and admins can view leave statistics
        return $user->isManagerOrAdmin();
    }
}
