<?php

namespace App\Policies;

use App\Models\MenuCategory;
use App\Models\User;

class MenuCategoryPolicy
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
        return $user->can('view-categories');
    }

    public function view(User $user, MenuCategory $category): bool
    {
        return $user->can('view-categories') && ($user->restaurant_id === $category->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create-categories');
    }

    public function update(User $user, MenuCategory $category): bool
    {
        return $user->can('edit-categories') && ($user->restaurant_id === $category->restaurant_id);
    }

    public function delete(User $user, MenuCategory $category): bool
    {
        return $user->can('delete-categories') && ($user->restaurant_id === $category->restaurant_id);
    }
}
