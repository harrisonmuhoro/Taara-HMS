<?php

namespace App\Services;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    /**
     * Check if a specific room is available for the given date range.
     */
    public function isRoomAvailable(
        int $roomId,
        string $checkIn,
        string $checkOut,
        ?int $excludeReservationId = null
    ): bool {
        $room = Room::find($roomId);
        if (!$room || !$room->is_active || in_array($room->operational_status, ['OUT_OF_ORDER', 'MAINTENANCE'])) {
            return false;
        }

        // Overlap query: requested_check_in < existing_check_out AND requested_check_out > existing_check_in
        $hasOverlap = $room->reservations()
            ->whereNotIn('status', ['CANCELLED', 'CHECKED_OUT', 'NO_SHOW'])
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            })
            ->exists();

        return !$hasOverlap;
    }

    /**
     * Get all available rooms for a branch within a date range, optionally filtered by room type.
     */
    public function getAvailableRooms(
        int $branchId,
        string $checkIn,
        string $checkOut,
        ?int $roomTypeId = null
    ): Collection {
        return Room::where('branch_id', $branchId)
            ->where('is_active', true)
            ->whereNotIn('operational_status', ['OUT_OF_ORDER', 'MAINTENANCE'])
            ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereNotIn('status', ['CANCELLED', 'CHECKED_OUT', 'NO_SHOW'])
                  ->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            })
            ->with(['roomType', 'floor'])
            ->orderBy('room_number')
            ->get();
    }
}
