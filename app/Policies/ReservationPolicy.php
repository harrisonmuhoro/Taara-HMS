<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('reservations.view');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservations.view') && $user->branch_id === $reservation->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('reservations.create');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservations.edit') && $user->branch_id === $reservation->branch_id;
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservations.cancel') && $user->branch_id === $reservation->branch_id;
    }

    public function confirm(User $user, Reservation $reservation): bool
    {
        return $user->hasPermission('reservations.confirm') && $user->branch_id === $reservation->branch_id;
    }
}
