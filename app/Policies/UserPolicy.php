<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        // Users can view their own profile, managers can view all
        return $user->id === $model->id || $user->isManagerOrAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        // Users can update their own profile, admins can update all
        return $user->id === $model->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        // Only admins can delete users, but not themselves
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
