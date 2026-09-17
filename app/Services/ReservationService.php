<?php

namespace App\Services;

use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\ReservationConflictException;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemNotificationMail;
use App\Models\Guest;

class ReservationService
{
    public function __construct(
        protected AvailabilityService $availabilityService
    ) {}

    public function createReservation(array $data, int $userId): Reservation
    {
        return DB::transaction(function () use ($data, $userId) {
            $checkIn = Carbon::parse($data['check_in_date']);
            $checkOut = Carbon::parse($data['check_out_date']);
            $nights = max(1, $checkIn->diffInDays($checkOut));

            $roomType = RoomType::findOrFail($data['room_type_id']);
            $roomId = $data['room_id'] ?? null;

            if ($roomId) {
                if (!$this->availabilityService->isRoomAvailable($roomId, $checkIn->toDateString(), $checkOut->toDateString())) {
                    throw new ReservationConflictException('The selected room is not available for the requested date range.');
                }
            } else {
                $availableRooms = $this->availabilityService->getAvailableRooms(
                    $data['branch_id'],
                    $checkIn->toDateString(),
                    $checkOut->toDateString(),
                    $roomType->id
                );

                if ($availableRooms->isEmpty()) {
                    throw new ReservationConflictException('No rooms of this type are available for the requested date range.');
                }
                $roomId = $availableRooms->first()->id;
            }

            // Server-side authoritative total calculation
            $baseRate = (float) ($data['base_rate'] ?? $roomType->base_rate);
            $subtotal = $baseRate * $nights;
            $discount = (float) ($data['discount_amount'] ?? 0.00);
            $taxRate = ((float) Setting::getByKey('tax_rate', (int) $data['branch_id'], 16.00)) / 100;
            $taxable = max(0, $subtotal - $discount);
            $taxAmount = round($taxable * $taxRate, 2);
            $totalAmount = round($taxable + $taxAmount, 2);

            $reservationNumber = 'RES-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));

            $reservation = Reservation::create([
                'branch_id' => $data['branch_id'],
                'reservation_number' => $reservationNumber,
                'guest_id' => $data['guest_id'],
                'room_type_id' => $roomType->id,
                'room_id' => $roomId,
                'booking_source_id' => $data['booking_source_id'],
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'base_rate' => $baseRate,
                'discount_amount' => $discount,
                'tax_amount' => $taxAmount,
                'service_charge' => 0.00,
                'total_amount' => $totalAmount,
                'deposit_amount' => $data['deposit_amount'] ?? 0.00,
                'special_requests' => $data['special_requests'] ?? null,
                'status' => $data['status'] ?? 'CONFIRMED',
                'created_by' => $userId,
            ]);

            AuditService::log('RESERVATION_CREATED', $reservation, null, $reservation->toArray());

            if ($guest = Guest::find($reservation->guest_id)) {
                Mail::to($guest->email)->queue(new SystemNotificationMail(
                    'Reservation confirmation ' . $reservation->reservation_number,
                    "Your reservation {$reservation->reservation_number} has been received for {$reservation->check_in_date->format('d M Y')} to {$reservation->check_out_date->format('d M Y')}.",
                    $reservation->reservation_number,
                ));
            }

            return $reservation;
        });
    }

    public function transitionStatus(Reservation $reservation, string $newStatus): Reservation
    {
        $allowedTransitions = [
            'PENDING' => ['CONFIRMED', 'CANCELLED'],
            'CONFIRMED' => ['CHECKED_IN', 'CANCELLED', 'NO_SHOW'],
            'CHECKED_IN' => ['CHECKED_OUT'],
            'CANCELLED' => [],
            'NO_SHOW' => [],
            'CHECKED_OUT' => [],
        ];

        $currentStatus = $reservation->status;
        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            throw new InvalidStatusTransitionException("Cannot transition reservation status from {$currentStatus} to {$newStatus}.");
        }

        $old = $reservation->toArray();
        $reservation->status = $newStatus;
        $reservation->save();

        AuditService::log('RESERVATION_STATUS_CHANGED', $reservation, $old, $reservation->toArray());

        return $reservation;
    }
}
