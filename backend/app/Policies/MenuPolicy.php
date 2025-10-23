<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
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
        return $user->can('view-menus');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->can('view-menus') && ($user->restaurant_id === $menu->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create-menus');
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->can('edit-menus') && ($user->restaurant_id === $menu->restaurant_id);
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->can('delete-menus') && ($user->restaurant_id === $menu->restaurant_id);
    }
}
