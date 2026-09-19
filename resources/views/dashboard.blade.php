<x-app-layout title="Dashboard">
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <x-breadcrumb :links="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />
                <h1 class="mt-2 text-2xl font-semibold text-ink dark:text-[#F0E6D8]">
                    {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                    {{ Str::words(Auth::user()->name, 1, '') }}
                </h1>
                <p class="mt-1 text-sm text-ink-muted">
                    {{ now()->format('l, j F Y') }}
                    @switch($dashboardMode)
                        @case('housekeeping') · Rooms that still need service @break
                        @case('front_desk') · Arrivals and departures @break
                        @case('finance') · Payments due today @break
                        @default · Shift briefing
                    @endswitch
                </p>
            </div>
            <a href="{{ route('reservations.create') }}" class="btn-primary shrink-0">New reservation</a>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" :dismissible="true" class="mb-5">{{ session('error') }}</x-alert>
    @endif

    @php
        $rate = $stats['total_rooms'] > 0
            ? round((($stats['total_rooms'] - $stats['available_rooms']) / $stats['total_rooms']) * 100)
            : 0;
    @endphp

    <div class="mb-6 grid grid-cols-2 gap-px overflow-hidden rounded border border-[#D9CFC0] bg-[#D9CFC0] dark:border-[#3A3228] dark:bg-[#3A3228] lg:grid-cols-5">
        @can('stays.check_in')
            <a href="{{ route('front-desk.check-in') }}" class="bg-surface p-4 dark:bg-surface-dark">
                <p class="text-sm text-ink-muted">Arrivals</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums">{{ $stats['today_checkins'] }}</p>
            </a>
        @endcan
        @can('stays.check_out')
            <a href="{{ route('front-desk.check-out') }}" class="bg-surface p-4 dark:bg-surface-dark">
                <p class="text-sm text-ink-muted">Departures</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums">{{ $stats['today_checkouts'] }}</p>
            </a>
        @endcan
        @can('housekeeping.view')
            <a href="{{ route('housekeeping.index') }}" class="bg-surface p-4 dark:bg-surface-dark">
                <p class="text-sm text-ink-muted">Dirty rooms</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums">{{ $stats['housekeeping_pending'] }}</p>
            </a>
        @endcan
        @can('maintenance.view')
            <a href="{{ route('maintenance.index') }}" class="bg-surface p-4 dark:bg-surface-dark">
                <p class="text-sm text-ink-muted">Open tickets</p>
                <p class="mt-1 text-3xl font-semibold tabular-nums">{{ $stats['maintenance_open'] }}</p>
            </a>
        @endcan
        <div class="bg-olive-600 p-4 text-[#F4EEE4]">
            <p class="text-sm text-olive-100">Occupied</p>
            <p class="mt-1 text-3xl font-semibold tabular-nums">{{ $rate }}%</p>
            <p class="mt-1 text-xs text-olive-100">{{ $stats['total_rooms'] - $stats['available_rooms'] }} of {{ $stats['total_rooms'] }} rooms</p>
        </div>
    </div>

    <div class="overflow-hidden rounded border border-[#D9CFC0] bg-surface dark:border-[#3A3228] dark:bg-surface-dark">
        <div class="flex items-center justify-between border-b border-[#D9CFC0] px-4 py-3 dark:border-[#3A3228]">
            <div>
                <h2 class="text-base font-semibold">Recent reservations</h2>
                <p class="text-xs text-ink-muted">Latest five on this property</p>
            </div>
            <a href="{{ route('reservations.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">All reservations</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                        <th><span class="sr-only">Open</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_reservations'] ?? [] as $res)
                        @php
                            $statusColors = [
                                'PENDING' => 'yellow',
                                'CONFIRMED' => 'blue',
                                'CHECKED_IN' => 'green',
                                'CHECKED_OUT' => 'gray',
                                'CANCELLED' => 'red',
                                'NO_SHOW' => 'orange',
                            ];
                        @endphp
                        <tr>
                            <td class="font-semibold text-brand-700">{{ $res->reference_number }}</td>
                            <td>
                                <p class="font-medium">{{ $res->guest->first_name ?? '' }} {{ $res->guest->last_name ?? 'N/A' }}</p>
                                <p class="text-xs text-ink-muted">{{ $res->guest->email ?? '' }}</p>
                            </td>
                            <td>{{ $res->room->room_number ?? 'TBD' }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->check_in_date)->format('j M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->check_out_date)->format('j M Y') }}</td>
                            <td><x-status-badge :color="$statusColors[$res->status] ?? 'gray'" :text="str_replace('_', ' ', $res->status)" /></td>
                            <td class="text-right">
                                <a href="{{ route('reservations.show', $res) }}" class="font-semibold text-brand-600 hover:text-brand-700">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-ink-muted">No reservations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
