<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->hasPermission('users.view')
            && ($user->isSuperAdmin() || $user->branch_id === $employee->branch_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermission('users.update')
            && ($user->isSuperAdmin() || $user->branch_id === $employee->branch_id);
    }
}
