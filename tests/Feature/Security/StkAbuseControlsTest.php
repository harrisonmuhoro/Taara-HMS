<?php

namespace Tests\Feature\Security;

use App\Models\Branch;
use App\Models\Hotel;
use App\Models\MpesaTransaction;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class StkAbuseControlsTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $hotel        = Hotel::create(['name' => 'Test Hotel']);
        $this->branch = Branch::create([
            'hotel_id' => $hotel->id,
            'name'     => 'Main Branch',
            'code'     => 'MAIN',
            'status'   => 'active',
        ]);
    }

    private function makeUserWithPermission(string $permission): User
    {
        $perm = Permission::firstOrCreate(
            ['name' => $permission],
            ['module' => 'finance', 'description' => 'Test permission']
        );

        $role = Role::create(['name' => 'TestCashier', 'description' => 'Test role']);
        $role->permissions()->attach($perm);

        $user = User::factory()->create([
            'status'    => 'active',
            'branch_id' => $this->branch->id,
        ]);
        $user->roles()->attach($role);

        return $user;
    }

    private function makeUserWithoutPermission(): User
    {
        $role = Role::firstOrCreate(
            ['name' => 'Housekeeper'],
            ['description' => 'No payment access']
        );

        $user = User::factory()->create([
            'status'    => 'active',
            'branch_id' => $this->branch->id,
        ]);
        $user->roles()->attach($role);

        return $user;
    }

    // F-01: permission guard on STK initiation
    public function test_user_without_payments_collect_gets_403_on_stk_initiate(): void
    {
        $user = $this->makeUserWithoutPermission();

        $this->actingAs($user)
            ->postJson('/api/mpesa/stkpush/initiate', [
                'phone'  => '0712345678',
                'amount' => '100',
            ])
            ->assertStatus(403);
    }

    // F-01: user WITH the permission is not blocked by the guard (may fail
    // for other reasons — we check it is NOT 403)
    public function test_user_with_payments_collect_passes_permission_guard(): void
    {
        $user = $this->makeUserWithPermission('payments.collect');

        $response = $this->actingAs($user)
            ->postJson('/api/mpesa/stkpush/initiate', [
                'phone'  => '0712345678',
                'amount' => '100',
            ]);

        $this->assertNotEquals(403, $response->status(), 'Expected permission guard to pass.');
    }

    // F-10: rate limiting — saturate the limiter with actual requests and assert 429
    public function test_sixth_stk_request_in_a_minute_is_rate_limited(): void
    {
        $user = $this->makeUserWithPermission('payments.collect');

        // Make 5 requests. They will return 422 due to missing invoice_id, but they hit the rate limiter.
        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)
                ->postJson('/api/mpesa/stkpush/initiate', [
                    'phone'  => '0712345678',
                    'amount' => '100',
                ]);
        }

        // The 6th request should hit the 429 Too Many Requests limit
        $this->actingAs($user)
            ->postJson('/api/mpesa/stkpush/initiate', [
                'phone'  => '0712345678',
                'amount' => '100',
            ])
            ->assertStatus(429);
    }
}
