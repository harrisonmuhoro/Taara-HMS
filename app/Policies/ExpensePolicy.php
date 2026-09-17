<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('expenses.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        if (!$user->hasPermission('expenses.view')) {
            return false;
        }
        return $user->isSuperAdmin() || $user->branch_id === $expense->branch_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('expenses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        if (!$user->hasPermission('expenses.create')) {
            return false;
        }
        return ($user->isSuperAdmin() || $user->branch_id === $expense->branch_id) && $expense->status === 'PENDING';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return false; // Typically expenses aren't hard deleted once logged, just voided/rejected
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Expense $expense): bool
    {
        if (!$user->hasPermission('expenses.approve')) {
            return false;
        }
        return ($user->isSuperAdmin() || $user->branch_id === $expense->branch_id) && $expense->status === 'PENDING';
    }
}
