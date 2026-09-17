<x-app-layout title="Reservation Calendar">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Reservations', 'url' => route('reservations.index')], ['label' => 'Calendar', 'url' => route('reservations.calendar')]]" />
        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div><h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reservation calendar</h1><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Room availability and bookings for {{ $monthStart->format('F Y') }}.</p></div>
            <a href="{{ route('reservations.create') }}" class="inline-flex w-fit rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">New reservation</a>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700/50 dark:bg-slate-800/60">
        <a href="{{ route('reservations.calendar', ['month' => $monthStart->copy()->subMonth()->format('Y-m')]) }}" class="rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">← Previous</a>
        <span class="font-semibold text-slate-900 dark:text-white">{{ $monthStart->format('F Y') }}</span>
        <a href="{{ route('reservations.calendar', ['month' => $monthStart->copy()->addMonth()->format('Y-m')]) }}" class="rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Next →</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-700/50 dark:bg-slate-800/60">
        <table class="min-w-[980px] w-full border-collapse text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/60"><tr><th class="sticky left-0 z-10 w-40 border-b border-r border-slate-200 bg-slate-50 px-4 py-3 font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Room</th>@foreach($days as $day)<th class="min-w-10 border-b border-slate-200 px-2 py-3 text-center font-medium text-slate-500 dark:border-slate-700 dark:text-slate-400"><span class="block">{{ $day->format('D') }}</span><span class="text-sm text-slate-900 dark:text-white">{{ $day->day }}</span></th>@endforeach</tr></thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70">
                @forelse($rooms as $room)
                    <tr><th class="sticky left-0 z-10 border-r border-slate-200 bg-white px-4 py-4 font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ $room->room_number }}</th>
                        @foreach($days as $day)
                            @php($booking = $reservations->first(fn ($reservation) => $reservation->room_id === $room->id && $reservation->check_in_date->lte($day) && $reservation->check_out_date->gt($day)))
                            <td class="h-20 border-r border-slate-100 p-1 align-top dark:border-slate-700/60">
                                @if($booking)
                                    <a href="{{ route('reservations.show', $booking) }}" title="{{ $booking->reservation_number }} · {{ $booking->guest?->first_name }} {{ $booking->guest?->last_name }} · {{ $booking->status }}" class="block h-full min-w-9 rounded-md bg-brand-100 p-1 text-[10px] leading-tight text-brand-900 hover:bg-brand-200 dark:bg-brand-900/60 dark:text-brand-100 dark:hover:bg-brand-800"><span class="font-semibold">{{ $booking->reservation_number }}</span><span class="mt-1 block truncate">{{ $booking->guest?->last_name }}</span><span class="mt-1 block truncate">{{ $booking->status }}</span></a>
                                @else <span class="block h-full rounded-md bg-slate-50 dark:bg-slate-900/30"></span> @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $days->count() + 1 }}" class="px-5 py-12 text-center text-slate-500">No room reservations scheduled for this month.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Each colored cell includes the reservation reference, guest surname, and status. Check-out dates are not occupied.</p>
</x-app-layout>
