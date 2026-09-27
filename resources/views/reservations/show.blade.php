<x-app-layout :title="'Reservation ' . $reservation->reservation_number">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Reservations', 'url' => route('reservations.index')],
            ['label' => $reservation->reservation_number, 'url' => '#'],
        ]" />
        <div class="mt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $reservation->reservation_number }}</h1>
                    @php
                        $statusColors = [
                            'PENDING'    => 'yellow', 'CONFIRMED'  => 'blue',  'CHECKED_IN' => 'green',
                            'CHECKED_OUT'=> 'gray',  'CANCELLED'  => 'red',   'NO_SHOW'    => 'orange',
                        ];
                    @endphp
                    <x-status-badge :color="$statusColors[$reservation->status] ?? 'gray'" :text="str_replace('_', ' ', $reservation->status)" />
                </div>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Created {{ $reservation->created_at->format('M j, Y \a\t h:i A') }}
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-2 shrink-0">
                @if ($reservation->status === 'PENDING')
                    <form method="POST" action="{{ route('reservations.confirm', $reservation) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Confirm
                        </button>
                    </form>
                @endif
                @if (in_array($reservation->status, ['PENDING', 'CONFIRMED']))
                    <a href="{{ route('reservations.edit', $reservation) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}"
                          onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Cancel Reservation
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" :dismissible="true" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Info ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Guest & Room Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Guest Card --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Guest</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold text-lg shrink-0 shadow-sm shadow-brand-500/25">
                            {{ strtoupper(substr($reservation->guest->first_name ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->guest->email }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->guest->phone }}</p>
                        </div>
                    </div>
                </div>

                {{-- Room Card --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Room</p>
                    <div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">Room {{ $reservation->room->room_number ?? 'TBD' }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $reservation->room->roomType->name ?? 'N/A' }}</p>
                        @if ($reservation->room->floor)
                            <p class="text-sm text-slate-400 dark:text-slate-500">Floor {{ $reservation->room->floor->floor_number }}</p>
                        @endif
                        <p class="mt-2 text-sm font-semibold text-brand-600 dark:text-brand-400">
                            KES {{ number_format($reservation->room->roomType->base_rate ?? 0, 2) }} / night
                        </p>
                    </div>
                </div>
            </div>

            {{-- Stay Details --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Stay Details</h2>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Check-in</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Check-out</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Nights</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($reservation->check_in_date)->diffInDays(\Carbon\Carbon::parse($reservation->check_out_date)) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Guests</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $reservation->adults }} adult{{ $reservation->adults > 1 ? 's' : '' }}
                            @if ($reservation->children > 0), {{ $reservation->children }} child{{ $reservation->children > 1 ? 'ren' : '' }}@endif
                        </dd>
                    </div>
                </dl>
                @if ($reservation->special_requests)
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <dt class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold mb-1.5">Special Requests</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $reservation->special_requests }}</dd>
                    </div>
                @endif
            </div>

            {{-- Folios / Financial --}}
            @if ($reservation->stays->isNotEmpty())
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Folios & Invoices</h2>
                    @foreach ($reservation->stays as $stay)
                        @foreach ($stay->folios as $folio)
                            <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-900/40 rounded-xl">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">Folio #{{ $folio->id }}</span>
                                    <x-status-badge :color="$folio->status === 'OPEN' ? 'blue' : 'green'" :text="$folio->status" />
                                </div>
                                <div class="flex justify-between text-sm text-slate-600 dark:text-slate-400">
                                    <span>Total Charges</span>
                                    <span class="font-semibold text-slate-900 dark:text-white">KES {{ number_format($folio->calculateSubtotal(), 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Financial Summary Sidebar ── --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Financial Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <dt>Base Rate</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">KES {{ number_format($reservation->base_rate, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <dt>Discount</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">- KES {{ number_format($reservation->discount_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <dt>Tax (VAT 16%)</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">KES {{ number_format($reservation->tax_amount, 2) }}</dd>
                    </div>
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-3">
                        <div class="flex justify-between font-semibold text-base text-slate-900 dark:text-white">
                            <dt>Total</dt>
                            <dd class="text-brand-600 dark:text-brand-400">KES {{ number_format($reservation->total_amount, 2) }}</dd>
                        </div>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400">
                        <dt>Deposit Paid</dt>
                        <dd class="font-medium text-emerald-600 dark:text-emerald-400">KES {{ number_format($reservation->deposit_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-600 dark:text-slate-400 font-semibold">
                        <dt>Balance Due</dt>
                        <dd class="text-slate-900 dark:text-white">KES {{ number_format($reservation->total_amount - $reservation->deposit_amount, 2) }}</dd>
                    </div>
                </dl>
                @if ($reservation->status !== 'CANCELLED' && $reservation->total_amount > $reservation->deposit_amount)
                    <button type="button" onclick="openDepositModal()" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/></svg>
                        Pay Deposit via M-Pesa
                    </button>
                @endif
            </div>

            {{-- Branch --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Branch</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $reservation->branch->name ?? 'N/A' }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->branch->city ?? '' }}, {{ $reservation->branch->country ?? '' }}</p>
            </div>
        </div>
    </div>

    <div id="deposit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Pay Reservation Deposit</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">An M-Pesa prompt will be sent to the phone below.</p>
                </div>
                <button type="button" onclick="closeDepositModal()" class="text-2xl leading-none text-slate-400 hover:text-slate-700 dark:hover:text-white">&times;</button>
            </div>
            <div id="deposit-form" class="mt-6 space-y-4">
                <div>
                    <label for="deposit-phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">M-Pesa Phone Number</label>
                    <input id="deposit-phone" type="tel" value="{{ $reservation->guest->phone ?? '' }}" placeholder="0712345678" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-white">
                </div>
                <div>
                    <label for="deposit-payment-amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Amount (KES)</label>
                    <input id="deposit-payment-amount" type="number" min="1" max="{{ max(0, $reservation->total_amount - $reservation->deposit_amount) }}" value="{{ max(0, $reservation->total_amount - $reservation->deposit_amount) }}" step="1" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-white">
                </div>
                <p id="deposit-message" class="hidden rounded-xl px-3 py-2 text-sm"></p>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeDepositModal()" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Cancel</button>
                    <button type="button" id="deposit-submit" onclick="sendDepositStkPush()" class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Send STK Push</button>
                </div>
            </div>
            <div id="deposit-waiting" class="hidden py-8 text-center">
                <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-4 border-emerald-100 border-t-emerald-600"></div>
                <p class="font-semibold text-slate-900 dark:text-white">Waiting for payment</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Enter your M-Pesa PIN on the phone.</p>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    let depositPollTimer;
    const depositModal = document.getElementById('deposit-modal');
    const depositMessage = document.getElementById('deposit-message');

    function openDepositModal() { depositModal.classList.remove('hidden'); depositModal.classList.add('flex'); }
    function closeDepositModal() { clearTimeout(depositPollTimer); depositModal.classList.add('hidden'); depositModal.classList.remove('flex'); }

    function showDepositMessage(message, error = false) {
        depositMessage.textContent = message;
        depositMessage.className = `rounded-xl px-3 py-2 text-sm ${error ? 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'}`;
    }

    async function sendDepositStkPush() {
        const button = document.getElementById('deposit-submit');
        const phone = document.getElementById('deposit-phone').value.trim();
        const amount = document.getElementById('deposit-payment-amount').value;
        if (!phone || !amount) { showDepositMessage('Enter a phone number and amount.', true); return; }
        button.disabled = true;
        try {
            const response = await fetch('/api/mpesa/stkpush/initiate', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ phone, amount, reservation_id: {{ $reservation->id }} })
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to initiate M-Pesa payment.');
            document.getElementById('deposit-form').classList.add('hidden');
            document.getElementById('deposit-waiting').classList.remove('hidden');
            pollDepositStatus(data.checkout_request_id);
        } catch (error) {
            button.disabled = false;
            showDepositMessage(error.message, true);
        }
    }

    async function pollDepositStatus(checkoutRequestId) {
        try {
            const response = await fetch(`/api/mpesa/status/${encodeURIComponent(checkoutRequestId)}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            if (data.status === 'completed') { window.location.reload(); return; }
            if (data.status === 'failed') { throw new Error(data.result_desc || 'M-Pesa payment failed.'); }
            depositPollTimer = setTimeout(() => pollDepositStatus(checkoutRequestId), 3000);
        } catch (error) {
            document.getElementById('deposit-form').classList.remove('hidden');
            document.getElementById('deposit-waiting').classList.add('hidden');
            document.getElementById('deposit-submit').disabled = false;
            showDepositMessage(error.message, true);
        }
    }
</script>
@endpush
</x-app-layout>
