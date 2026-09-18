<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Hotel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffBranchAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_branch_from_the_configuration_endpoint(): void
    {
        $hotel = Hotel::create(['name' => 'Test Hotel']);
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::create(['name' => 'Super Administrator']));

        $this->actingAs($superAdmin)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Hotel branches')
            ->assertSee('Add branch');

        $this->actingAs($superAdmin)
            ->post(route('configuration.branches.store'), [
                'name' => 'Airport Branch',
                'code' => 'AIRPORT',
                'address' => 'Airport Road',
                'phone' => '+254700000000',
                'email' => 'airport@example.test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('branches', [
            'hotel_id' => $hotel->id,
            'name' => 'Airport Branch',
            'code' => 'AIRPORT',
        ]);
    }

    public function test_super_admin_can_assign_new_staff_to_a_selected_branch(): void
    {
        $hotel = Hotel::create(['name' => 'Test Hotel']);
        $mainBranch = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Main Branch', 'code' => 'MAIN']);
        $secondBranch = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Airport Branch', 'code' => 'AIRPORT']);
        $department = Department::create(['branch_id' => $secondBranch->id, 'name' => 'Front Desk']);

        $superAdmin = User::factory()->create(['branch_id' => $mainBranch->id]);
        $superAdmin->roles()->attach(Role::create(['name' => 'Super Administrator']));

        $this->actingAs($superAdmin)
            ->post(route('staff.store'), [
                'branch_id' => $secondBranch->id,
                'first_name' => 'Airport',
                'last_name' => 'Manager',
                'email' => 'airport.manager@example.test',
                'phone' => '+254700000000',
                'department_id' => $department->id,
                'position' => 'Hotel Manager',
                'hire_date' => now()->toDateString(),
                'create_account' => false,
            ])
            ->assertRedirectToRoute('staff.index');

        $this->assertDatabaseHas('employees', [
            'first_name' => 'Airport',
            'last_name' => 'Manager',
            'branch_id' => $secondBranch->id,
            'department_id' => $department->id,
        ]);
    }

    public function test_staff_cannot_assign_an_employee_to_another_branch(): void
    {
        $hotel = Hotel::create(['name' => 'Test Hotel']);
        $mainBranch = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Main Branch', 'code' => 'MAIN']);
        $secondBranch = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Airport Branch', 'code' => 'AIRPORT']);
        $mainDepartment = Department::create(['branch_id' => $mainBranch->id, 'name' => 'Front Desk']);
        $secondDepartment = Department::create(['branch_id' => $secondBranch->id, 'name' => 'Front Desk']);

        $role = Role::create(['name' => 'Staff Creator']);
        $permission = \App\Models\Permission::create(['name' => 'users.create', 'module' => 'staff']);
        $role->permissions()->attach($permission);
        $user = User::factory()->create(['branch_id' => $mainBranch->id]);
        $user->roles()->attach($role);

        $this->actingAs($user)
            ->from(route('staff.create'))
            ->post(route('staff.store'), [
                'branch_id' => $secondBranch->id,
                'first_name' => 'Invalid',
                'last_name' => 'Assignment',
                'email' => 'invalid.assignment@example.test',
                'department_id' => $secondDepartment->id,
                'position' => 'Receptionist',
                'hire_date' => now()->toDateString(),
                'create_account' => false,
            ])
            ->assertSessionHasErrors('branch_id');

        $this->assertDatabaseMissing('employees', ['email' => 'invalid.assignment@example.test']);
    }
}
