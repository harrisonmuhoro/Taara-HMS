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
            </div>

            {{-- Branch --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Branch</p>
                <p class="font-semibold text-slate-900 dark:text-white">{{ $reservation->branch->name ?? 'N/A' }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $reservation->branch->city ?? '' }}, {{ $reservation->branch->country ?? '' }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
