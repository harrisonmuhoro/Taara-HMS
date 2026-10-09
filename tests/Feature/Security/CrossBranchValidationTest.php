<?php

namespace Tests\Feature\Security;

use App\Models\Branch;
use App\Models\ExpenseCategory;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\InventoryCategory;
use App\Models\Invoice;
use App\Models\MaintenanceCategory;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossBranchValidationTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branchA;
    private Branch $branchB;
    private User $userA;
    private Room $roomB;
    private MaintenanceCategory $maintCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $hotel = Hotel::create(['name' => 'Grand Horizon']);
        $this->branchA = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Branch A', 'code' => 'BRA', 'status' => 'active']);
        $this->branchB = Branch::create(['hotel_id' => $hotel->id, 'name' => 'Branch B', 'code' => 'BRB', 'status' => 'active']);

        $role = Role::firstOrCreate(['name' => 'StaffRole'], ['description' => 'Staff']);
        $perms = [
            'maintenance.create', 'maintenance.view', 'maintenance.edit',
            'reservations.create', 'reservations.view',
            'inventory.adjust', 'inventory.view',
            'expenses.create', 'expenses.view',
            'payments.collect',
        ];
        foreach ($perms as $p) {
            $perm = Permission::firstOrCreate(['name' => $p], ['module' => 'test', 'description' => $p]);
            $role->permissions()->syncWithoutDetaching([$perm->id]);
        }

        $this->userA = User::factory()->create([
            'status' => 'active',
            'branch_id' => $this->branchA->id,
        ]);
        $this->userA->roles()->attach($role);

        $floorB = Floor::create(['branch_id' => $this->branchB->id, 'name' => 'Floor 1', 'floor_number' => 1]);
        $roomTypeB = RoomType::create(['branch_id' => $this->branchB->id, 'name' => 'Deluxe', 'base_rate' => 100]);
        $this->roomB = Room::create([
            'branch_id' => $this->branchB->id,
            'floor_id' => $floorB->id,
            'room_type_id' => $roomTypeB->id,
            'room_number' => 'B101',
        ]);

        $this->maintCategory = MaintenanceCategory::firstOrCreate(['name' => 'Plumbing'], ['description' => 'Pipes']);
    }

    public function test_user_cannot_create_maintenance_ticket_with_room_from_another_branch(): void
    {
        $response = $this->actingAs($this->userA)->post('/maintenance', [
            'room_id' => $this->roomB->id,
            'category_id' => $this->maintCategory->id,
            'title' => 'Leaking faucet',
            'description' => 'Water leaking',
            'priority' => 'LOW',
        ]);

        $response->assertSessionHasErrors('room_id');
    }

    public function test_user_cannot_create_reservation_with_room_from_another_branch(): void
    {
        $guestA = Guest::create([
            'branch_id' => $this->branchA->id,
            'guest_number' => 'GST-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0711111111',
            'id_type' => 'NATIONAL_ID',
            'id_number' => '12345678',
        ]);

        $response = $this->actingAs($this->userA)->post('/reservations', [
            'guest_id' => $guestA->id,
            'room_id' => $this->roomB->id,
            'branch_id' => $this->branchA->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 1,
        ]);

        $response->assertSessionHasErrors('room_id');
    }

    public function test_user_cannot_adjust_stock_for_product_from_another_branch(): void
    {
        $invCat = InventoryCategory::create([
            'branch_id' => $this->branchB->id,
            'name' => 'Linens B',
        ]);

        $productB = Product::create([
            'branch_id' => $this->branchB->id,
            'category_id' => $invCat->id,
            'name' => 'Towels B',
            'sku' => 'TWL-B',
            'cost_price' => 10,
            'current_stock' => 50,
            'reorder_level' => 5,
        ]);

        $response = $this->actingAs($this->userA)->post('/inventory/adjustments', [
            'product_id' => $productB->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => 5,
            'notes' => 'Recount',
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_user_cannot_create_expense_with_category_from_another_branch(): void
    {
        $categoryB = ExpenseCategory::create([
            'branch_id' => $this->branchB->id,
            'name' => 'Utilities B',
        ]);

        $response = $this->actingAs($this->userA)->post('/finance/expenses', [
            'category_id' => $categoryB->id,
            'amount' => 500,
            'expense_date' => now()->toDateString(),
            'description' => 'Electric bill',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_user_cannot_initiate_stk_for_invoice_from_another_branch(): void
    {
        $guestB = Guest::create([
            'branch_id' => $this->branchB->id,
            'guest_number' => 'GST-B-001',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $invoiceB = Invoice::create([
            'branch_id' => $this->branchB->id,
            'guest_id' => $guestB->id,
            'invoice_number' => 'INV-B-001',
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
            'grand_total' => 500,
            'subtotal' => 500,
            'paid_amount' => 0,
            'balance_due' => 500,
            'status' => 'ISSUED',
        ]);

        $response = $this->actingAs($this->userA)->postJson('/api/mpesa/stkpush/initiate', [
            'phone' => '0712345678',
            'amount' => '500',
            'invoice_id' => $invoiceB->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('invoice_id');
    }
}
