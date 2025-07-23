<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project)
    {
        // Users can view projects they're assigned to, managers can view all
        return $user->projects->contains($project) || $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project)
    {
        return $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project)
    {
        return $user->isManagerOrAdmin();
    }
}
