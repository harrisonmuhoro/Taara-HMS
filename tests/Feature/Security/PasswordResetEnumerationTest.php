<?php

namespace Tests\Feature\Security;

use App\Models\Branch;
use App\Models\Hotel;
use App\Models\User;
use App\Notifications\QueuedResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetEnumerationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $hotel = Hotel::create(['name' => 'Test Hotel']);
        $branch = Branch::create([
            'hotel_id' => $hotel->id,
            'name'     => 'Main Branch',
            'code'     => 'MAIN',
            'status'   => 'active',
        ]);

        $this->user = User::factory()->create([
            'email'     => 'registered@example.com',
            'status'    => 'active',
            'branch_id' => $branch->id,
        ]);
    }

    public function test_existing_email_returns_generic_status_message(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => $this->user->email,
        ]);

        $response->assertSessionHas('status', 'If that address is registered, a reset link has been sent.');
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_non_existent_email_returns_identical_generic_status_message(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        // Must NOT return auth.failed or "We can't find a user with that email address"
        $response->assertSessionHas('status', 'If that address is registered, a reset link has been sent.');
        $response->assertSessionDoesntHaveErrors();
    }

    public function test_reset_password_notification_implements_should_queue(): void
    {
        $notification = new QueuedResetPasswordNotification('dummy-token');
        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    public function test_password_reset_route_is_rate_limited(): void
    {
        Notification::fake();

        $email = 'spam-test@example.com';

        // Limit is 3 per minute
        for ($i = 0; $i < 3; $i++) {
            $this->post('/forgot-password', ['email' => $email]);
        }

        // 4th request must be throttled with 429
        $response = $this->post('/forgot-password', ['email' => $email]);
        $response->assertStatus(429);
    }
}
