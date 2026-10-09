<?php

namespace App\Services;

use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\ReservationConflictException;
use App\Mail\SystemNotificationMail;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected DocumentNumberService $documentNumbers,
    ) {}

    public function createReservation(array $data, int $userId): Reservation
    {
        $reservation = DB::transaction(function () use ($data, $userId) {
            $checkIn = Carbon::parse($data['check_in_date']);
            $checkOut = Carbon::parse($data['check_out_date']);
            $nights = max(1, $checkIn->diffInDays($checkOut));

            $roomType = RoomType::findOrFail($data['room_type_id']);
            $roomId = $data['room_id'] ?? null;

            if ($roomId) {
                // Serialize reservations for the same room. The availability query alone
                // is vulnerable to two concurrent requests both observing a free room.
                Room::query()->lockForUpdate()->findOrFail($roomId);
                if (! $this->availabilityService->isRoomAvailable(
                    $roomId, $checkIn->toDateString(), $checkOut->toDateString()
                )) {
                    throw new ReservationConflictException(
                        'The selected room is not available for the requested date range.'
                    );
                }
            } else {
                $availableRooms = $this->availabilityService->getAvailableRooms(
                    $data['branch_id'],
                    $checkIn->toDateString(),
                    $checkOut->toDateString(),
                    $roomType->id
                );

                if ($availableRooms->isEmpty()) {
                    throw new ReservationConflictException(
                        'No rooms of this type are available for the requested date range.'
                    );
                }
                $roomId = null;
                foreach ($availableRooms as $candidate) {
                    Room::query()->lockForUpdate()->findOrFail($candidate->id);
                    if ($this->availabilityService->isRoomAvailable(
                        (int) $candidate->id, $checkIn->toDateString(), $checkOut->toDateString()
                    )) {
                        $roomId = $candidate->id;
                        break;
                    }
                }
                if (! $roomId) {
                    throw new ReservationConflictException(
                        'No rooms of this type are available for the requested date range.'
                    );
                }
            }

            // Server-side authoritative total calculation
            $baseRateMinor = Money::toMinor($roomType->base_rate);
            $subtotalMinor = $baseRateMinor * $nights;
            $discountMinor = max(0, Money::toMinor($data['discount_amount'] ?? '0.00'));
            $taxRate = Money::percentageBasisPoints(Setting::getByKey('tax_rate', (int) $data['branch_id'], '0.00'));
            $taxableMinor = max(0, $subtotalMinor - $discountMinor);
            $taxAmountMinor = Money::percentageOf($taxableMinor, $taxRate);
            $totalAmountMinor = $taxableMinor + $taxAmountMinor;
            $depositRate = Money::percentageBasisPoints(Setting::getByKey('deposit_rate', (int) $data['branch_id'], '40.00'));
            $depositAmountMinor = Money::percentageOf($totalAmountMinor, $depositRate);

            $reservation = Reservation::create([
                'branch_id' => $data['branch_id'],
                'reservation_number' => $this->documentNumbers->next((int) $data['branch_id'], 'RES'),
                'guest_id' => $data['guest_id'],
                'room_type_id' => $roomType->id,
                'room_id' => $roomId,
                'booking_source_id' => $data['booking_source_id'],
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'base_rate' => Money::fromMinor($baseRateMinor),
                'discount_amount' => Money::fromMinor($discountMinor),
                'tax_amount' => Money::fromMinor($taxAmountMinor),
                'service_charge' => 0.00,
                'total_amount' => Money::fromMinor($totalAmountMinor),
                'deposit_amount' => Money::fromMinor($depositAmountMinor),
                'deposit_paid' => $depositAmountMinor <= 0,
                'deposit_paid_at' => $depositAmountMinor <= 0 ? now() : null,
                'special_requests' => $data['special_requests'] ?? null,
                'status' => $depositAmountMinor > 0 ? 'PENDING_DEPOSIT' : 'CONFIRMED',
                'created_by' => $userId,
            ]);

            return $reservation;
        });

        // Audit + guest email AFTER commit: never fire for a rolled-back reservation.
        AuditService::log('RESERVATION_CREATED', $reservation, null, $reservation->toArray());

        if ($guest = Guest::find($reservation->guest_id)) {
            Mail::to($guest->email)->queue(new SystemNotificationMail(
                'Reservation confirmation '.$reservation->reservation_number,
                "Your reservation {$reservation->reservation_number} has been received for "
                    .$reservation->check_in_date->format('d M Y').' to '
                    .$reservation->check_out_date->format('d M Y').'.',
                $reservation->reservation_number,
            ));
        }

        return $reservation;
    }

    public function transitionStatus(Reservation $reservation, string $newStatus): Reservation
    {
        $result = DB::transaction(function () use ($reservation, $newStatus) {
            $locked = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);
            $old = $locked->toArray();

            $allowedTransitions = [
                'PENDING_DEPOSIT' => ['CONFIRMED', 'CANCELLED'],
                'PENDING' => ['CONFIRMED', 'CANCELLED'],
                'CONFIRMED' => ['CHECKED_IN', 'CANCELLED', 'NO_SHOW'],
                'CHECKED_IN' => ['CHECKED_OUT'],
                'CANCELLED' => [],
                'NO_SHOW' => [],
                'CHECKED_OUT' => [],
            ];

            $currentStatus = $locked->status;
            if (! in_array($newStatus, $allowedTransitions[$currentStatus] ?? [], true)) {
                throw new InvalidStatusTransitionException(
                    "Cannot transition reservation status from {$currentStatus} to {$newStatus}."
                );
            }

            $locked->status = $newStatus;
            $locked->save();

            return ['reservation' => $locked, 'old' => $old];
        });

        AuditService::log(
            'RESERVATION_STATUS_CHANGED',
            $result['reservation'],
            $result['old'],
            $result['reservation']->toArray()
        );

        return $result['reservation'];
    }
}
