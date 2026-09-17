<?php

namespace App\Policies;

use App\Models\Guest;
use App\Models\User;

class GuestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('guests.view');
    }

    public function view(User $user, Guest $guest): bool
    {
        return $user->hasPermission('guests.view')
            && ($user->isSuperAdmin() || $user->branch_id === $guest->branch_id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('guests.create');
    }

    public function update(User $user, Guest $guest): bool
    {
        return $user->hasPermission('guests.edit')
            && ($user->isSuperAdmin() || $user->branch_id === $guest->branch_id);
    }

    public function delete(User $user, Guest $guest): bool
    {
        return $user->hasPermission('guests.delete')
            && ($user->isSuperAdmin() || $user->branch_id === $guest->branch_id);
    }
}
