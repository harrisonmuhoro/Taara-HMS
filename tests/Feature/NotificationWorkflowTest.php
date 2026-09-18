<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_reading_a_notification_marks_it_read_and_redirects_to_its_resource(): void
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'RESERVATION',
            'title' => 'Reservation confirmed',
            'message' => 'A reservation was confirmed.',
            'reference_type' => 'reservation',
            'reference_id' => 123,
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification))
            ->assertRedirect(route('reservations.show', 123));

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_mark_all_as_read_only_updates_the_authenticated_users_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'SYSTEM',
            'title' => 'Own alert',
            'message' => 'Read this.',
            'is_read' => false,
        ]);
        $otherNotification = Notification::create([
            'user_id' => $otherUser->id,
            'type' => 'SYSTEM',
            'title' => 'Other alert',
            'message' => 'Leave this unread.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'is_read' => true,
        ]);
        $this->assertFalse($otherNotification->fresh()->is_read);
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $otherUser->id,
            'type' => 'SYSTEM',
            'title' => 'Private alert',
            'message' => 'Private.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification))
            ->assertForbidden();

        $this->assertFalse($notification->fresh()->is_read);
    }
}
