<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InactiveUserAndSessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    // ─── F-02: Inactive accounts cannot log in ───────────────────────────────

    public function test_rejects_login_for_inactive_user_with_correct_password(): void
    {
        $user = User::factory()->create([
            'status'   => 'inactive',
            'password' => Hash::make('correct-password'),
        ]);

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_allows_active_user_to_log_in(): void
    {
        $user = User::factory()->create([
            'status'   => 'active',
            'password' => Hash::make('correct-password'),
        ]);

        $this->post('/login', ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_redirects_inactive_user_with_live_session_to_login(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        // Active user can reach dashboard
        $this->actingAs($user)->get('/dashboard')->assertOk();

        // Admin deactivates the user
        $user->update(['status' => 'inactive']);

        // The next request must be bounced
        $this->actingAs($user->fresh())->get('/dashboard')
            ->assertRedirect('/login');
    }

    // ─── F-05: Password change revokes other sessions ────────────────────────

    public function test_changing_password_revokes_all_other_active_sessions(): void
    {
        if (config('session.driver') !== 'database') {
            $this->markTestSkipped('Requires SESSION_DRIVER=database to test session revocation.');
        }

        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        // Seed a fake "other" session for this user
        DB::table('sessions')->insert([
            'id'            => 'other-session-id',
            'user_id'       => $user->id,
            'ip_address'    => '1.2.3.4',
            'user_agent'    => 'OtherBrowser',
            'payload'       => base64_encode(serialize([])),
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($user)->patch('/password', [
            'current_password'      => 'old-password',
            'password'              => 'New-Secure-Password1!',
            'password_confirmation' => 'New-Secure-Password1!',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('sessions', ['id' => 'other-session-id']);
    }

    public function test_password_reset_via_link_deletes_all_user_sessions(): void
    {
        if (config('session.driver') !== 'database') {
            $this->markTestSkipped('Requires SESSION_DRIVER=database to test session revocation.');
        }

        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        // Seed a fake open session (simulating a stolen/parallel session)
        DB::table('sessions')->insert([
            'id'            => 'stolen-session-id',
            'user_id'       => $user->id,
            'ip_address'    => '5.6.7.8',
            'user_agent'    => 'AttackerBrowser',
            'payload'       => base64_encode(serialize([])),
            'last_activity' => now()->timestamp,
        ]);

        $token = app('auth.password.broker')->createToken($user);

        $this->post('/reset-password', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'New-Secure-Password1!',
            'password_confirmation' => 'New-Secure-Password1!',
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('sessions', ['id' => 'stolen-session-id']);
    }
}
