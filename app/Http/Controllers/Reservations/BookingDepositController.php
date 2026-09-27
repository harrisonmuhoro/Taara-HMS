<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\User;
use App\Services\MpesaService;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDepositController extends Controller
{
    public function __construct(
        protected MpesaService $mpesaService,
        protected ReservationService $reservationService,
    ) {}

    public function initiate(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);

        if ($reservation->status === 'CONFIRMED') {
            return response()->json(['success' => false, 'message' => 'Deposit already settled.'], 422);
        }

        if ($reservation->status !== 'PENDING_DEPOSIT') {
            return response()->json(['success' => false, 'message' => 'This reservation is not awaiting a deposit.'], 422);
        }

        if ($reservation->deposit_paid) {
            return response()->json(['success' => false, 'message' => 'Deposit already settled.'], 422);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(?:\+?254|0)\d{9}$/'],
        ]);

        $pendingTransaction = $reservation->mpesaTransactions()
            ->where('transaction_type', 'STK_PUSH')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingTransaction) {
            return response()->json([
                'success' => true,
                'message' => 'A deposit prompt is already pending on the guest phone.',
                'checkout_request_id' => $pendingTransaction->checkout_request_id,
            ]);
        }

        if ((float) $reservation->deposit_amount <= 0) {
            $this->reservationService->transitionStatus($reservation, 'CONFIRMED');
            $reservation->update(['deposit_paid' => true, 'deposit_paid_at' => now()]);

            return response()->json(['success' => true, 'message' => 'No deposit is required.', 'confirmed' => true]);
        }

        $response = $this->mpesaService->stkPush(
            $validated['phone'],
            $reservation->deposit_amount,
            'DEPOSIT-' . $reservation->id,
            'Advance Room Deposit',
        );

        if (($response['ResponseCode'] ?? null) !== '0') {
            return response()->json([
                'success' => false,
                'message' => $response['errorMessage'] ?? $response['ResponseDescription'] ?? 'Failed to initiate the deposit STK push.',
            ], 422);
        }

        $reservation->mpesaTransactions()->create([
            'transaction_type' => 'STK_PUSH',
            'merchant_request_id' => $response['MerchantRequestID'] ?? null,
            'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
            'phone_number' => $validated['phone'],
            'amount' => $reservation->deposit_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deposit STK push sent. Please check the guest phone.',
            'checkout_request_id' => $response['CheckoutRequestID'],
        ]);
    }

    public function extend(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);
        abort_unless($request->user()->isSuperAdmin() || $request->user()->hasRole('Hotel Manager'), 403);

        if ($reservation->status !== 'PENDING_DEPOSIT' || $reservation->deposit_paid) {
            return response()->json(['success' => false, 'message' => 'Only unpaid deposit reservations can be extended.'], 422);
        }

        $validated = $request->validate(['hours' => ['required', 'integer', 'min:1', 'max:48']]);
        $reservation->update(['deposit_expires_at' => now()->addHours((int) $validated['hours'])]);

        return response()->json(['success' => true, 'expires_at' => $reservation->deposit_expires_at]);
    }

    public function waive(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);
        abort_unless($request->user()->isSuperAdmin(), 403);

        if ($reservation->status !== 'PENDING_DEPOSIT' || $reservation->deposit_paid) {
            return response()->json(['success' => false, 'message' => 'This deposit cannot be waived.'], 422);
        }

        DB::transaction(function () use ($reservation): void {
            $reservation->update([
                'deposit_amount' => 0,
                'deposit_paid' => true,
                'deposit_paid_at' => now(),
                'deposit_expires_at' => null,
            ]);
        });
        $this->reservationService->transitionStatus($reservation, 'CONFIRMED');

        return response()->json(['success' => true, 'message' => 'Deposit waived and reservation confirmed.']);
    }

    public static function notifyFrontDesk(Reservation $reservation, string $title, string $message): void
    {
        User::query()
            ->with('roles')
            ->where('branch_id', $reservation->branch_id)
            ->where('status', 'active')
            ->get()
            ->filter(fn (User $user) => $user->isSuperAdmin() || $user->hasPermission('reservations.view'))
            ->each(fn (User $user) => Notification::create([
                'user_id' => $user->id,
                'type' => 'RESERVATION_DEPOSIT',
                'title' => $title,
                'message' => $message,
                'reference_type' => 'reservation',
                'reference_id' => $reservation->id,
            ]));
    }
}
