<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-restaurants');
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        if ($user->can('view-restaurants')) {
            return $user->restaurant_id === $restaurant->id || $user->hasRole('super-admin');
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('create-restaurants');
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        if ($user->can('edit-restaurants')) {
            return $user->restaurant_id === $restaurant->id || $user->hasRole('super-admin');
        }
        return false;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        if ($user->can('delete-restaurants')) {
            return $user->restaurant_id === $restaurant->id || $user->hasRole('super-admin');
        }
        return false;
    }
}
