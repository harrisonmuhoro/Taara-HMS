<?php

namespace App\Jobs;

use App\Http\Controllers\Reservations\BookingDepositController;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CancelExpiredDepositReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReservationService $reservationService): void
    {
        Reservation::query()
            ->where('status', 'PENDING_DEPOSIT')
            ->where('deposit_paid', false)
            ->whereNotNull('deposit_expires_at')
            ->where('deposit_expires_at', '<', now())
            ->orderBy('id')
            ->chunkById(100, function ($reservations) use ($reservationService): void {
                foreach ($reservations as $reservation) {
                    $reservation->refresh();

                    if ($reservation->status !== 'PENDING_DEPOSIT' || $reservation->deposit_paid || ! $reservation->deposit_expires_at?->isPast()) {
                        continue;
                    }

                    try {
                        $reservationService->transitionStatus($reservation, 'CANCELLED');
                        BookingDepositController::notifyFrontDesk(
                            $reservation,
                            'Reservation cancelled: deposit expired',
                            "Reservation {$reservation->reservation_number} was cancelled because its deposit was not received before the deadline.",
                        );
                    } catch (\Throwable $exception) {
                        Log::error('Failed to cancel expired deposit reservation.', [
                            'reservation_id' => $reservation->id,
                            'exception' => $exception,
                        ]);
                    }
                }
            });
    }
}
