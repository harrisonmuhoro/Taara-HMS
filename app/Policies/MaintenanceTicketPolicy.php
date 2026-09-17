<?php

namespace App\Policies;

use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaintenanceTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('maintenance.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceTicket $maintenanceTicket): bool
    {
        if (!$user->hasPermission('maintenance.view')) {
            return false;
        }
        return $user->branch_id === $maintenanceTicket->branch_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('maintenance.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaintenanceTicket $maintenanceTicket): bool
    {
        if (!$user->hasPermission('maintenance.edit')) {
            return false;
        }
        return $user->branch_id === $maintenanceTicket->branch_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceTicket $maintenanceTicket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MaintenanceTicket $maintenanceTicket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MaintenanceTicket $maintenanceTicket): bool
    {
        return false;
    }
}
