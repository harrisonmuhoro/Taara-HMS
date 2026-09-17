<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class PosPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('restaurant.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('restaurant.manage');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasPermission('restaurant.manage');
    }
}
