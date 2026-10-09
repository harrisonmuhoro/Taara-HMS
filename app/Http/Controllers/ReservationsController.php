<?php

namespace App\Http\Controllers;

use App\Exceptions\ReservationConflictException;
use App\Models\BookingSource;
use App\Models\Branch;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Setting;
use App\Services\AvailabilityService;
use App\Services\ReservationService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ReservationsController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
        protected AvailabilityService $availabilityService,
    ) {}

    /**
     * List all reservations with filtering and pagination.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);
        $query = Reservation::with(['guest', 'room', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest('created_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                    ->orWhereHas('guest', fn ($g) => $g->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('check_in_from')) {
            $query->whereDate('check_in_date', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->whereDate('check_in_date', '<=', $request->check_in_to);
        }

        $summaryQuery = clone $query;
        $reservationIds = (clone $summaryQuery)->select('reservations.id');
        $paidFromTransactions = Money::toMinor(DB::table('mpesa_transactions')
            ->whereIn('reservation_id', $reservationIds)
            ->where('status', 'completed')
            ->sum('amount'));
        $historicalDeposits = Money::toMinor((clone $summaryQuery)
            ->where('deposit_paid', true)
            ->whereNull('deposit_receipt_no')
            ->sum('deposit_amount'));
        $totalAmount = Money::toMinor((clone $summaryQuery)->sum('total_amount'));
        $depositAmount = Money::toMinor((clone $summaryQuery)->sum('deposit_amount'));
        $paidAmount = $paidFromTransactions + $historicalDeposits;
        $financialSummary = [
            'total_amount' => Money::fromMinor($totalAmount),
            'deposit_amount' => Money::fromMinor($depositAmount),
            'paid_amount' => Money::fromMinor($paidAmount),
        ];
        $financialSummary['balance_due'] = Money::fromMinor(max(0, $totalAmount - $paidAmount));

        $reservations = $query->paginate(15)->withQueryString();

        $statuses = ['PENDING_DEPOSIT', 'PENDING', 'CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT', 'CANCELLED', 'NO_SHOW'];

        return view('reservations.index', compact('reservations', 'statuses', 'financialSummary'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $this->authorize('create', Reservation::class);
        $guests = Guest::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);
        $rooms = Room::with('roomType')
            ->where('operational_status', 'AVAILABLE')
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('room_number')
            ->get();
        $branches = Branch::where('status', 'active')
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereKey(auth()->user()->branch_id))
            ->get(['id', 'name']);
        $taxRates = $branches->mapWithKeys(
            fn (Branch $branch) => [$branch->id => Setting::getByKey('tax_rate', (int) $branch->id, '0.00')]
        );

        return view('reservations.create', compact('guests', 'rooms', 'branches', 'taxRates'));
    }

    public function calendar(Request $request)
    {
        $this->authorize('viewAny', Reservation::class);

        $month = $request->input('month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = now()->format('Y-m');
        }

        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $days = collect(range(0, $monthStart->daysInMonth - 1))->map(fn ($offset) => $monthStart->copy()->addDays($offset));
        $reservations = Reservation::with(['guest', 'room'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->whereNotIn('status', ['CANCELLED', 'NO_SHOW'])
            ->where('check_in_date', '<=', $monthEnd->toDateString())
            ->where('check_out_date', '>=', $monthStart->toDateString())
            ->orderBy('check_in_date')
            ->get();

        $rooms = $reservations->pluck('room')->filter()->unique('id')->sortBy('room_number');

        return view('reservations.calendar', compact('reservations', 'rooms', 'days', 'monthStart', 'monthEnd'));
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class);

        $branchId = $request->user()->branch_id;
        $isSuper = $request->user()->isSuperAdmin();

        $validated = $request->validate([
            'guest_id' => [
                'required',
                Rule::exists('guests', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->when(! $isSuper, fn ($r) => $r->where('id', $branchId)),
            ],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $room = Room::whereKey($validated['room_id'])->firstOrFail();
        $guest = Guest::whereKey($validated['guest_id'])->firstOrFail();
        abort_unless(
            auth()->user()->isSuperAdmin()
                ? $room->branch_id === (int) $validated['branch_id'] && $guest->branch_id === (int) $validated['branch_id']
                : (int) $validated['branch_id'] === (int) auth()->user()->branch_id
                    && $room->branch_id === auth()->user()->branch_id
                    && $guest->branch_id === auth()->user()->branch_id,
            403
        );

        try {
            $bookingSource = BookingSource::where('branch_id', $validated['branch_id'])
                ->where('type', 'DIRECT')
                ->where('status', 'active')
                ->orderBy('id')
                ->first();
            if (! $bookingSource) {
                return back()->withInput()->withErrors(['branch_id' => 'No active direct booking source is configured for this branch.']);
            }

            $reservation = $this->reservationService->createReservation(
                array_merge($validated, [
                    'room_type_id' => $room->room_type_id,
                    'booking_source_id' => $bookingSource->id,
                    'special_requests' => $validated['notes'] ?? null,
                ]),
                auth()->id(),
            );

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', "Reservation {$reservation->reservation_number} created successfully.");

        } catch (ReservationConflictException $e) {
            return back()->withInput()->withErrors(['room_id' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Reservation creation failed', [
                'user_id' => auth()->id(),
                'branch_id' => $validated['branch_id'] ?? null,
                'guest_id' => $validated['guest_id'] ?? null,
                'room_id' => $validated['room_id'] ?? null,
                'exception' => $e,
            ]);

            return back()->withInput()->with('error', 'Failed to create reservation: '.$e->getMessage());
        }
    }

    /**
     * Display a specific reservation.
     */
    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load([
            'guest',
            'room.roomType',
            'room.floor',
            'branch',
            'mpesaTransactions',
            'stays.folios.items',
            'stays.folios.invoices.payments',
        ]);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing a reservation.
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if (! in_array($reservation->status, ['PENDING_DEPOSIT', 'PENDING', 'CONFIRMED'])) {
            return back()->with('error', 'Only pending-deposit, pending, or confirmed reservations can be edited.');
        }

        $guests = Guest::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
        $rooms = Room::with('roomType')
            ->where(function ($query) use ($reservation) {
                $query->where('operational_status', 'AVAILABLE')
                    ->orWhere('id', $reservation->room_id);
            })
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('room_number')
            ->get();
        $branches = Branch::where('status', 'active')
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereKey(auth()->user()->branch_id))
            ->get(['id', 'name']);

        return view('reservations.edit', compact('reservation', 'guests', 'rooms', 'branches'));
    }

    /**
     * Update a reservation.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        if (! in_array($reservation->status, ['PENDING_DEPOSIT', 'PENDING', 'CONFIRMED'])) {
            return back()->with('error', 'Only pending-deposit, pending, or confirmed reservations can be updated.');
        }

        $validated = $request->validate([
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($reservation, $validated) {
                $lockedReservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);

                if ($lockedReservation->room_id) {
                    Room::query()->lockForUpdate()->findOrFail($lockedReservation->room_id);
                    if (! $this->availabilityService->isRoomAvailable(
                        (int) $lockedReservation->room_id,
                        $validated['check_in_date'],
                        $validated['check_out_date'],
                        (int) $lockedReservation->id,
                    )) {
                        throw new \RuntimeException('The room is not available for the updated date range.');
                    }
                }

                $nights = max(1, Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date'])));
                $subtotalMinor = Money::toMinor($lockedReservation->base_rate) * $nights;
                $discountMinor = Money::toMinor($lockedReservation->discount_amount);
                $taxRate = Money::percentageBasisPoints(Setting::getByKey('tax_rate', (int) $lockedReservation->branch_id, '0.00'));
                $taxableMinor = max(0, $subtotalMinor - $discountMinor);
                $taxAmountMinor = Money::percentageOf($taxableMinor, $taxRate);
                $totalAmountMinor = $taxableMinor + $taxAmountMinor + Money::toMinor($lockedReservation->service_charge);

                $lockedReservation->update([
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'adults' => $validated['adults'],
                    'children' => $validated['children'] ?? 0,
                    'tax_amount' => Money::fromMinor($taxAmountMinor),
                    'total_amount' => Money::fromMinor($totalAmountMinor),
                    'special_requests' => $validated['notes'] ?? null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Confirm a pending reservation.
     */
    public function confirm(Reservation $reservation)
    {
        $this->authorize('confirm', $reservation);
        try {
            $this->reservationService->transitionStatus($reservation, 'CONFIRMED');

            return back()->with('success', 'Reservation confirmed.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $this->authorize('cancel', $reservation);
        try {
            $this->reservationService->transitionStatus($reservation, 'CANCELLED');

            return redirect()
                ->route('reservations.index')
                ->with('success', "Reservation {$reservation->reservation_number} has been cancelled.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
