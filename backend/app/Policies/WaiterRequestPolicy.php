<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WaiterRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class WaiterRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('view-waiter-requests');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WaiterRequest $waiterRequest): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('view-waiter-requests');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('create-waiter-requests');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WaiterRequest $waiterRequest): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('edit-waiter-requests');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WaiterRequest $waiterRequest): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('delete-waiter-requests');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WaiterRequest $waiterRequest): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('restore-waiter-requests');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WaiterRequest $waiterRequest): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasPermissionTo('force-delete-waiter-requests');
    }
}
