<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Branch;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    /**
     * Display a listing of the rooms for housekeeping.
     */
    public function index(Request $request)
    {
        $this->authorize('housekeeping.view');
        $query = Room::with(['roomType', 'floor', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('room_number');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('housekeeping_status', $request->status);
        }

        $rooms = $query->paginate(24)->withQueryString();
        $branches = Branch::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereKey(auth()->user()->branch_id))
            ->get();

        return view('housekeeping.index', compact('rooms', 'branches'));
    }

    /**
     * Update the room housekeeping status (e.g., from DIRTY to CLEANING).
     */
    public function updateStatus(Request $request, Room $room)
    {
        $this->authorize('housekeeping.update');
        abort_unless(
            auth()->user()->isSuperAdmin() || $room->branch_id === auth()->user()->branch_id,
            403
        );
        $request->validate([
            'status' => ['required', 'in:DIRTY,CLEANING,CLEAN,INSPECTED'],
        ]);

        $room->update(['housekeeping_status' => $request->status]);

        return back()->with('success', "Room {$room->room_number} status updated to " . str_replace('_', ' ', $request->status) . ".");
    }
}
