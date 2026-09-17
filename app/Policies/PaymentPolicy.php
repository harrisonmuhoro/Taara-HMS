<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('payments.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermission('payments.view') && $user->branch_id === $payment->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('payments.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermission('payments.edit') && $user->branch_id === $payment->branch_id;
    }
    
    public function refund(User $user, Payment $payment): bool
    {
        return $user->hasPermission('payments.refund') && $user->branch_id === $payment->branch_id;
    }
}
