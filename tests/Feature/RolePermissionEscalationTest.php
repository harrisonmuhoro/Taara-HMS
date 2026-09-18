<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionEscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_role_manager_cannot_grant_permissions_they_do_not_have(): void
    {
        $roleManager = Role::create(['name' => 'Role Manager']);
        $rolesManage = Permission::create([
            'name' => 'roles.manage',
            'module' => 'roles',
            'description' => 'Manage roles',
        ]);
        $roleManager->permissions()->attach($rolesManage);

        $manager = User::factory()->create();
        $manager->roles()->attach($roleManager);

        $targetRole = Role::create(['name' => 'Front Desk']);
        $restrictedPermission = Permission::create([
            'name' => 'settings.manage',
            'module' => 'settings',
            'description' => 'Manage settings',
        ]);

        $this->actingAs($manager)
            ->put(route('roles.permissions.update', $targetRole), [
                'permissions' => [$restrictedPermission->id],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('permission_role', [
            'role_id' => $targetRole->id,
            'permission_id' => $restrictedPermission->id,
        ]);
    }

    public function test_a_role_manager_can_grant_permissions_they_already_have(): void
    {
        $roleManager = Role::create(['name' => 'Role Manager']);
        $rolesManage = Permission::create([
            'name' => 'roles.manage',
            'module' => 'roles',
            'description' => 'Manage roles',
        ]);
        $reservationsView = Permission::create([
            'name' => 'reservations.view',
            'module' => 'reservations',
            'description' => 'View reservations',
        ]);
        $roleManager->permissions()->attach([$rolesManage->id, $reservationsView->id]);

        $manager = User::factory()->create();
        $manager->roles()->attach($roleManager);
        $targetRole = Role::create(['name' => 'Front Desk']);

        $this->actingAs($manager)
            ->put(route('roles.permissions.update', $targetRole), [
                'permissions' => [$reservationsView->id],
            ])
            ->assertRedirectToRoute('roles.index');

        $this->assertDatabaseHas('permission_role', [
            'role_id' => $targetRole->id,
            'permission_id' => $reservationsView->id,
        ]);
    }
}
