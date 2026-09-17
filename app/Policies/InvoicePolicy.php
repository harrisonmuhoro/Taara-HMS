<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('invoices.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('invoices.view') && $user->branch_id === $invoice->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('invoices.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('invoices.edit') && $user->branch_id === $invoice->branch_id;
    }

    public function void(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('invoices.void') && $user->branch_id === $invoice->branch_id;
    }
}
