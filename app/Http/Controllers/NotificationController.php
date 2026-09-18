<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->update(['is_read' => true]);

        $referenceType = (string) $notification->reference_type;
        $destination = match (true) {
            str_starts_with($referenceType, 'maintenance:') => route('maintenance.index'),
            $referenceType === 'stay' || str_starts_with($referenceType, 'checkouts:') => route('front-desk.check-out'),
            $referenceType === 'product' => route('inventory.products.index'),
            $referenceType === 'reservation' => route('reservations.show', $notification->reference_id),
            default => route('reservations.index'),
        };

        return redirect($destination);
    }
}
