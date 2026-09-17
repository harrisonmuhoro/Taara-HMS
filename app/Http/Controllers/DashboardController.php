<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Guest;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $rooms = Room::when($branchId, fn ($query) => $query->where('branch_id', $branchId));
        $reservations = Reservation::when($branchId, fn ($query) => $query->where('branch_id', $branchId));
        $guests = Guest::when($branchId, fn ($query) => $query->where('branch_id', $branchId));

        $totalRooms = (clone $rooms)->count();

        // Rooms with no active stay are considered available
        $occupiedRooms = (clone $rooms)->whereHas('stays', function ($q) {
            $q->where('status', 'ACTIVE');
        })->count();

        $stats = [
            'total_rooms'          => $totalRooms,
            'available_rooms'      => $totalRooms - $occupiedRooms,
            'active_reservations'  => (clone $reservations)->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])->count(),
            'total_guests'         => (clone $guests)->count(),
            'recent_reservations'  => (clone $reservations)->with(['guest', 'room'])
                ->latest()
                ->limit(5)
                ->get(),
            'today_checkins' => (clone $reservations)->whereDate('check_in_date', today())->whereIn('status', ['PENDING', 'CONFIRMED'])->count(),
            'today_checkouts' => (clone $reservations)->whereDate('check_out_date', today())->where('status', 'CHECKED_IN')->count(),
            'housekeeping_pending' => (clone $rooms)->whereIn('housekeeping_status', ['DIRTY', 'INSPECT'])->count(),
            'maintenance_open' => MaintenanceTicket::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->whereIn('status', ['OPEN', 'IN_PROGRESS'])->count(),
        ];

        $dashboardMode = match (true) {
            auth()->user()->hasPermission('housekeeping.manage') => 'housekeeping',
            auth()->user()->hasPermission('stays.check_in') || auth()->user()->hasPermission('reservations.create') => 'front_desk',
            auth()->user()->hasPermission('invoices.view') || auth()->user()->hasPermission('payments.view') => 'finance',
            default => 'management',
        };

        return view('dashboard', compact('stats', 'dashboardMode'));
    }
}
