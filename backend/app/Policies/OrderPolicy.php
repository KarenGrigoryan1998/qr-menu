<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
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
        return $user->can('view-orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('view-orders') && ($user->restaurant_id === $order->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create-orders');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can('edit-orders') && ($user->restaurant_id === $order->restaurant_id);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->can('delete-orders') && ($user->restaurant_id === $order->restaurant_id);
    }
}
