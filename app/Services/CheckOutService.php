<?php

namespace App\Services;

use App\Exceptions\UnsettledBalanceException;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\Stay;
use Illuminate\Support\Facades\DB;

class CheckOutService
{
    public function checkOut(Stay $stay, int $userId): Stay
    {
        return DB::transaction(function () use ($stay, $userId) {
            $stay = Stay::query()
                ->lockForUpdate()
                ->findOrFail($stay->id);

            if ($stay->status !== 'ACTIVE') {
                throw new \RuntimeException("Stay #{$stay->id} is already completed or inactive.");
            }

            $folio = $stay->activeFolio;
            if ($folio && $folio->invoice) {
                $invoice = $folio->invoice;
                $invoice->recalculateTotals();

                if ((float) $invoice->balance_due > 0.01) {
                    throw new UnsettledBalanceException(
                        "Cannot check out stay. Invoice {$invoice->invoice_number} has an outstanding balance of KES " . number_format($invoice->balance_due, 2)
                    );
                }
            }

            // Close Folio
            if ($folio) {
                $folio->status = 'CLOSED';
                $folio->closed_at = now();
                $folio->save();
            }

            // Close Stay
            $stay->status = 'COMPLETED';
            $stay->actual_check_out = now();
            $stay->checked_out_by = $userId;
            $stay->save();

            // Update Reservation
            if ($stay->reservation) {
                $stay->reservation->status = 'CHECKED_OUT';
                $stay->reservation->save();
            }

            // Room Lifecycle: OCCUPIED -> AVAILABLE / DIRTY
            $room = Room::query()->lockForUpdate()->findOrFail($stay->room_id);
            $room->operational_status = 'AVAILABLE';
            $room->housekeeping_status = 'DIRTY';
            $room->save();

            // Create Housekeeping Task
            HousekeepingTask::create([
                'branch_id' => $stay->branch_id,
                'room_id' => $room->id,
                'task_type' => 'CLEANING',
                'priority' => 'HIGH',
                'status' => 'PENDING',
                'notes' => "Automatic checkout cleaning for Room {$room->room_number} (Stay #{$stay->id})",
            ]);

            AuditService::log('GUEST_CHECKED_OUT', $stay, null, ['stay_id' => $stay->id, 'room_number' => $room->room_number]);

            return $stay;
        });
    }
}
