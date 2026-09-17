<?php

namespace App\Services;

use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Folio;
use App\Models\FolioItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Stay;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckInService
{
    public function checkIn(Reservation $reservation, int $userId): Stay
    {
        return DB::transaction(function () use ($reservation, $userId) {
            $reservation = Reservation::query()
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if (!in_array($reservation->status, ['CONFIRMED', 'PENDING'])) {
                throw new InvalidStatusTransitionException("Reservation {$reservation->reservation_number} is not in a valid state for check-in.");
            }

            if (Stay::where('reservation_id', $reservation->id)->where('status', 'ACTIVE')->exists()) {
                throw new InvalidStatusTransitionException("Reservation {$reservation->reservation_number} already has an active stay.");
            }

            $room = Room::query()->lockForUpdate()->findOrFail($reservation->room_id);
            if (in_array($room->operational_status, ['OCCUPIED', 'OUT_OF_ORDER', 'MAINTENANCE'], true)) {
                throw new InvalidStatusTransitionException("Room {$room->room_number} is not available for check-in.");
            }

            // Create Stay
            $stay = Stay::create([
                'branch_id' => $reservation->branch_id,
                'reservation_id' => $reservation->id,
                'guest_id' => $reservation->guest_id,
                'room_id' => $room->id,
                'actual_check_in' => now(),
                'expected_check_out' => Carbon::parse(
                    $reservation->check_out_date->toDateString() . ' ' . Setting::getByKey('check_out_time', (int) $reservation->branch_id, '10:00')
                ),
                'status' => 'ACTIVE',
                'checked_in_by' => $userId,
            ]);

            // Open Folio
            $folio = Folio::create([
                'branch_id' => $reservation->branch_id,
                'stay_id' => $stay->id,
                'guest_id' => $reservation->guest_id,
                'folio_number' => 'FOL-' . $room->room_number . '-' . strtoupper(substr(uniqid(), -5)),
                'status' => 'OPEN',
                'opened_at' => now(),
            ]);

            // Add Initial Room Charge to Folio
            $nights = $reservation->nights;
            $subtotal = (float) $reservation->base_rate * $nights;
            FolioItem::create([
                'folio_id' => $folio->id,
                'item_type' => 'ROOM',
                'description' => "Room {$room->room_number} Nightly Stay ({$nights} Nights)",
                'quantity' => $nights,
                'unit_price' => $reservation->base_rate,
                'discount_amount' => $reservation->discount_amount,
                'tax_amount' => $reservation->tax_amount,
                'total_amount' => $reservation->total_amount,
            ]);

            // Issue Invoice
            $invoice = Invoice::create([
                'branch_id' => $reservation->branch_id,
                'folio_id' => $folio->id,
                'guest_id' => $reservation->guest_id,
                'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6)),
                'subtotal' => $subtotal,
                'discount_amount' => $reservation->discount_amount,
                'tax_amount' => $reservation->tax_amount,
                'service_charge' => $reservation->service_charge,
                'grand_total' => $reservation->total_amount,
                'amount_paid' => $reservation->deposit_amount,
                'balance_due' => max(0, (float)$reservation->total_amount - (float)$reservation->deposit_amount),
                'status' => ($reservation->deposit_amount >= $reservation->total_amount) ? 'PAID' : (($reservation->deposit_amount > 0) ? 'PARTIALLY_PAID' : 'ISSUED'),
                'issued_at' => now(),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Room {$room->room_number} Stay ({$nights} Nights)",
                'quantity' => $nights,
                'unit_price' => $reservation->base_rate,
                'discount_amount' => $reservation->discount_amount,
                'tax_amount' => $reservation->tax_amount,
                'total_amount' => $reservation->total_amount,
            ]);

            // Update Reservation & Room Status
            $reservation->status = 'CHECKED_IN';
            $reservation->save();

            $room->operational_status = 'OCCUPIED';
            $room->save();

            AuditService::log('GUEST_CHECKED_IN', $stay, null, ['stay_id' => $stay->id, 'folio_id' => $folio->id]);

            return $stay;
        });
    }
}
