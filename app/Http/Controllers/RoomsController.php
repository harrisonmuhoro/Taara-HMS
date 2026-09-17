<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomsController extends Controller
{
    /**
     * Display the room board (all rooms with status).
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Room::class);
        $query = Room::with(['roomType', 'floor', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('room_number');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('operational_status', $request->status);
        }

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        $rooms = $query->get();
        $branches = Branch::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereKey(auth()->user()->branch_id))
            ->get();
        $roomTypes = RoomType::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->get();
        
        $stats = [
            'total' => $rooms->count(),
            'available' => $rooms->where('operational_status', 'AVAILABLE')->count(),
            'occupied' => $rooms->where('operational_status', 'OCCUPIED')->count(),
            'maintenance' => $rooms->where('operational_status', 'MAINTENANCE')->count(),
            'cleaning' => $rooms->where('housekeeping_status', 'CLEANING')->count(),
        ];

        return view('rooms.index', compact('rooms', 'branches', 'roomTypes', 'stats'));
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        $this->authorize('view', $room);
        $room->load(['roomType', 'floor', 'branch']);
        
        // Get active or upcoming reservations for this room
        $reservations = $room->reservations()
            ->with('guest')
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'CHECKED_IN'])
            ->where('check_out_date', '>=', Carbon::today())
            ->orderBy('check_in_date')
            ->get();

        return view('rooms.show', compact('room', 'reservations'));
    }
}
