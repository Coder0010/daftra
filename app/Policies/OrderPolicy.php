<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the product.
     *
     * @param User    $user
     * @param Order $order
     *
     * @return bool
     */
    public function show(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the product.
     *
     * @param User    $user
     * @param Order $order
     *
     * @return bool
     */
    public function update(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}

