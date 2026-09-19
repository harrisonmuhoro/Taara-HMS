<x-app-layout title="Front Desk Check-in">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Check-in', 'url' => route('front-desk.check-in')]]" />
        <h1 class="mt-2 text-2xl font-semibold text-ink dark:text-[#F0E6D8]">Check-in</h1>
        <p class="mt-1 text-sm text-ink-muted">Guests due today or waiting from earlier.</p>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>
    @endif

    <div class="overflow-hidden rounded border border-[#D9CFC0] bg-surface dark:border-[#3A3228] dark:bg-surface-dark">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Reservation</th>
                        <th>Room</th>
                        <th>Arrival</th>
                        <th class="text-right"> </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td class="font-medium">{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</td>
                            <td>{{ $reservation->reservation_number }}</td>
                            <td>{{ $reservation->room->room_number ?? 'Unassigned' }}</td>
                            <td>{{ $reservation->check_in_date->format('j M Y') }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('front-desk.check-in.process', $reservation) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary">Check in</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-ink-muted">Nobody is waiting to check in.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reservations->hasPages())
            <div class="border-t border-[#D9CFC0] px-4 py-3 dark:border-[#3A3228]">{{ $reservations->links() }}</div>
        @endif
    </div>
</x-app-layout>
