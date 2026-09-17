<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'description' => 'View main dashboard'],

            // Front Desk & Reservations
            ['name' => 'reservations.view', 'description' => 'View reservations'],
            ['name' => 'reservations.create', 'description' => 'Create reservations'],
            ['name' => 'reservations.edit', 'description' => 'Edit reservations'],
            ['name' => 'reservations.delete', 'description' => 'Delete reservations'],
            ['name' => 'guests.view', 'description' => 'View guest profiles'],
            ['name' => 'guests.create', 'description' => 'Create guest profiles'],
            ['name' => 'rooms.view', 'description' => 'View rooms and status'],
            ['name' => 'rooms.manage', 'description' => 'Manage rooms (add/edit)'],

            // Operations
            ['name' => 'housekeeping.view', 'description' => 'View housekeeping dashboard'],
            ['name' => 'housekeeping.manage', 'description' => 'Manage housekeeping tasks'],
            ['name' => 'maintenance.view', 'description' => 'View maintenance tickets'],
            ['name' => 'maintenance.create', 'description' => 'Create maintenance tickets'],
            ['name' => 'maintenance.update', 'description' => 'Update maintenance tickets'],
            ['name' => 'restaurant.view', 'description' => 'View restaurant POS'],
            ['name' => 'restaurant.manage', 'description' => 'Manage restaurant menus & orders'],
            
            // Inventory
            ['name' => 'inventory.view', 'description' => 'View inventory and stock levels'],
            ['name' => 'inventory.create_product', 'description' => 'Create products & suppliers'],
            ['name' => 'inventory.purchase', 'description' => 'Create and manage purchase orders'],
            ['name' => 'inventory.adjust', 'description' => 'Adjust stock levels manually'],

            // Admin & Finance
            ['name' => 'invoices.view', 'description' => 'View invoices'],
            ['name' => 'payments.view', 'description' => 'View payments'],
            ['name' => 'reports.view', 'description' => 'View system reports'],
            ['name' => 'users.view', 'description' => 'View staff and users'],
            ['name' => 'users.create', 'description' => 'Create staff and users'],
            ['name' => 'users.update', 'description' => 'Update staff and users'],
            ['name' => 'roles.manage', 'description' => 'Manage roles and permissions'],
            ['name' => 'settings.view', 'description' => 'View and edit settings'],
            ['name' => 'settings.manage', 'description' => 'Manage system and hotel configuration'],
            ['name' => 'audit_logs.view', 'description' => 'View audit logs and activity history'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], ['description' => $perm['description']]);
        }

        // Create default roles if they don't exist
        $superAdmin = Role::firstOrCreate(['name' => 'Super Administrator'], ['description' => 'Full access to all features']);
        // Keep the role name aligned with DatabaseSeeder and the documented demo account.
        $manager = Role::firstOrCreate(['name' => 'Hotel Manager'], ['description' => 'General management access']);
        $frontDesk = Role::firstOrCreate(['name' => 'Front Desk'], ['description' => 'Reception and reservations access']);
        $housekeeping = Role::firstOrCreate(['name' => 'Housekeeping'], ['description' => 'Housekeeping staff access']);

        // Managers can view and update staff, but only administrators manage RBAC.
        $managerPerms = Permission::whereNotIn('name', ['roles.manage', 'settings.view', 'settings.manage', 'audit_logs.view'])->pluck('id');
        $manager->permissions()->sync($managerPerms);

        // Attach permissions to Front Desk
        $frontDeskPerms = Permission::whereIn('name', [
            'dashboard.view',
            'reservations.view', 'reservations.create', 'reservations.edit',
            'guests.view', 'guests.create',
            'rooms.view',
            'invoices.view', 'payments.view'
        ])->pluck('id');
        $frontDesk->permissions()->sync($frontDeskPerms);

        // Attach permissions to Housekeeping
        $hkPerms = Permission::whereIn('name', [
            'rooms.view',
            'housekeeping.view', 'housekeeping.manage',
            'maintenance.create'
        ])->pluck('id');
        $housekeeping->permissions()->sync($hkPerms);

        // Assign Super Admin role to user ID 1
        $user = User::find(1);
        if ($user && !$user->hasRole('Super Administrator')) {
            $user->roles()->attach($superAdmin->id);
        }
    }
}
