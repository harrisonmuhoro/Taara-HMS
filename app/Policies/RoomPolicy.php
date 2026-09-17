<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('rooms.view');
    }

    public function view(User $user, Room $room): bool
    {
        return $user->hasPermission('rooms.view') && $user->branch_id === $room->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('rooms.create');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasPermission('rooms.edit') && $user->branch_id === $room->branch_id;
    }

    public function manageStatus(User $user, Room $room): bool
    {
        return $user->hasPermission('rooms.manage_status') && $user->branch_id === $room->branch_id;
    }
}
