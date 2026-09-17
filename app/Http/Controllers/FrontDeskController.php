<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Stay;
use App\Services\CheckInService;
use App\Services\CheckOutService;
use Illuminate\Http\Request;

class FrontDeskController extends Controller
{
    public function checkIn(Request $request)
    {
        $this->authorize('stays.check_in');

        $reservations = Reservation::with(['guest', 'room.roomType'])
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->whereDate('check_in_date', '<=', now()->toDateString())
            ->orderBy('check_in_date')
            ->paginate(20)
            ->withQueryString();

        return view('front-desk.check-in', compact('reservations'));
    }

    public function processCheckIn(Reservation $reservation, CheckInService $checkInService)
    {
        $this->authorize('stays.check_in');
        abort_unless(
            auth()->user()->isSuperAdmin() || $reservation->branch_id === auth()->user()->branch_id,
            403
        );

        try {
            $stay = $checkInService->checkIn($reservation, auth()->id());

            return redirect()->route('front-desk.check-in')
                ->with('success', "Guest checked in successfully. Stay #{$stay->id} is now active.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkOut(Request $request)
    {
        $this->authorize('stays.check_out');

        $stays = Stay::with(['guest', 'room', 'activeFolio.invoice'])
            ->where('status', 'ACTIVE')
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest('actual_check_in')
            ->paginate(20)
            ->withQueryString();

        return view('front-desk.check-out', compact('stays'));
    }

    public function processCheckOut(Stay $stay, CheckOutService $checkOutService)
    {
        $this->authorize('stays.check_out');
        abort_unless(
            auth()->user()->isSuperAdmin() || $stay->branch_id === auth()->user()->branch_id,
            403
        );

        try {
            $checkOutService->checkOut($stay, auth()->id());

            return redirect()->route('front-desk.check-out')
                ->with('success', "Stay #{$stay->id} checked out successfully. Room marked dirty.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
