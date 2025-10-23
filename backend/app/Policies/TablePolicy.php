<?php

namespace App\Policies;

use App\Models\Table;
use App\Models\User;

class TablePolicy
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
        return $user->can('view-tables');
    }

    public function view(User $user, Table $table): bool
    {
        return $user->can('view-tables') && ($user->restaurant_id === $table->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create-tables');
    }

    public function update(User $user, Table $table): bool
    {
        return $user->can('edit-tables') && ($user->restaurant_id === $table->restaurant_id);
    }

    public function delete(User $user, Table $table): bool
    {
        return $user->can('delete-tables') && ($user->restaurant_id === $table->restaurant_id);
    }
}
