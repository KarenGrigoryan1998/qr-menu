<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
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
        return $user->can('view-payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view-payments') && ($user->restaurant_id === $payment->restaurant_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create-payments');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('edit-payments') && ($user->restaurant_id === $payment->restaurant_id);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('delete-payments') && ($user->restaurant_id === $payment->restaurant_id);
    }
}
