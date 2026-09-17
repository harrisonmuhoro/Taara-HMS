<x-app-layout title="Dashboard">
    <x-slot name="header">
        <div>
            <x-breadcrumb :links="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />
            <div class="mt-3 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-light text-slate-900 dark:text-white">
                        Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                        <em class="text-brand-600 dark:text-brand-400 not-italic">{{ Str::words(Auth::user()->name, 1, '') }}</em>
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ now()->format('l, F j, Y') }} &middot;
                        @switch($dashboardMode)
                            @case('housekeeping') Today’s room service priorities. @break
                            @case('front_desk') Today’s arrivals, departures, and guest activity. @break
                            @case('finance') Today’s revenue and payment activity. @break
                            @default Here’s what’s happening across your hotel today.
                        @endswitch
                    </p>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('reservations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/30 transition-all duration-200 hover:shadow-brand-500/40 hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Reservation
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" :dismissible="true" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700/50 dark:bg-slate-800/60">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">{{ str_replace('_', ' ', $dashboardMode) }} workspace</p><p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Your dashboard is tailored to the permissions assigned to your role.</p></div>
            <div class="flex flex-wrap gap-2">
                @if($dashboardMode === 'housekeeping')<a href="{{ route('housekeeping.index') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Open housekeeping</a>@elseif($dashboardMode === 'front_desk')<a href="{{ route('reservations.index') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Open reservations</a>@elseif($dashboardMode === 'finance')<a href="{{ route('finance.invoices') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">Open financials</a>@else<a href="{{ route('reports.index') }}" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">View reports</a>@endif
            </div>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @can('stays.check_in')
            <a href="{{ route('front-desk.check-in') }}" class="rounded-2xl border border-sky-200 bg-sky-50 p-5 transition hover:border-sky-400 dark:border-sky-900/60 dark:bg-sky-950/20"><p class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-300">Today’s check-ins</p><p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['today_checkins'] }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Open front desk queue →</p></a>
        @endcan
        @can('stays.check_out')
            <a href="{{ route('front-desk.check-out') }}" class="rounded-2xl border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-400 dark:border-amber-900/60 dark:bg-amber-950/20"><p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">Today’s check-outs</p><p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['today_checkouts'] }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Open departure queue →</p></a>
        @endcan
        @can('housekeeping.view')
            <a href="{{ route('housekeeping.index') }}" class="rounded-2xl border border-teal-200 bg-teal-50 p-5 transition hover:border-teal-400 dark:border-teal-900/60 dark:bg-teal-950/20"><p class="text-xs font-semibold uppercase tracking-wider text-teal-700 dark:text-teal-300">Housekeeping attention</p><p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['housekeeping_pending'] }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Rooms awaiting service →</p></a>
        @endcan
        @can('maintenance.view')
            <a href="{{ route('maintenance.index') }}" class="rounded-2xl border border-rose-200 bg-rose-50 p-5 transition hover:border-rose-400 dark:border-rose-900/60 dark:bg-rose-950/20"><p class="text-xs font-semibold uppercase tracking-wider text-rose-700 dark:text-rose-300">Open maintenance</p><p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['maintenance_open'] }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Review active tickets →</p></a>
        @endcan
    </div>

    {{-- ─── KPI Cards Row ──────────────────────────────────────────── --}}
    <div class="dashboard-stat-grid grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        {{-- Total Rooms --}}
        <div class="dashboard-stat group relative bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 hover:border-brand-500/40 hover:shadow-lg hover:shadow-brand-500/10 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rooms</p>
                    <p class="mt-2 text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_rooms']) }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">All branches combined</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                </svg>
                <span><strong>{{ $stats['available_rooms'] }}</strong> available now</span>
            </div>
        </div>

        {{-- Active Reservations --}}
        <div class="dashboard-stat group relative bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 hover:border-sky-500/40 hover:shadow-lg hover:shadow-sky-500/10 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Reservations</p>
                    <p class="mt-2 text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['active_reservations']) }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Confirmed & checked-in</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Updated just now</span>
            </div>
        </div>

        {{-- Total Guests --}}
        <div class="dashboard-stat group relative bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 hover:border-teal-500/40 hover:shadow-lg hover:shadow-teal-500/10 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Guests</p>
                    <p class="mt-2 text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_guests']) }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Registered in system</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50 flex items-center gap-1.5 text-xs text-teal-600 dark:text-teal-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Guest profiles stored</span>
            </div>
        </div>

        {{-- Occupancy Rate --}}
        <div class="dashboard-stat dashboard-stat-primary group relative bg-brand-600 rounded-2xl p-6 hover:shadow-lg hover:shadow-brand-500/30 transition-all duration-300 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-brand-200 uppercase tracking-wider">Occupancy Rate</p>
                    @php
                        $rate = $stats['total_rooms'] > 0
                            ? round((($stats['total_rooms'] - $stats['available_rooms']) / $stats['total_rooms']) * 100)
                            : 0;
                    @endphp
                    <p class="mt-2 text-4xl font-bold text-white">{{ $rate }}<span class="text-2xl">%</span></p>
                    <p class="mt-1 text-xs text-brand-200">{{ $stats['total_rooms'] - $stats['available_rooms'] }} of {{ $stats['total_rooms'] }} rooms occupied</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="w-full bg-white/20 rounded-full h-1.5">
                    <div class="bg-white rounded-full h-1.5 transition-all duration-1000" style="width: {{ $rate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Recent Reservations Table ──────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700/50">
            <div>
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Recent Reservations</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Latest 5 reservations across all branches</p>
            </div>
            <a href="{{ route('reservations.index') }}" class="text-xs font-medium text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors">
                View all →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ref #</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Room</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse($stats['recent_reservations'] ?? [] as $res)
                        @php
                            $statusColors = [
                                'PENDING'    => 'yellow',
                                'CONFIRMED'  => 'blue',
                                'CHECKED_IN' => 'green',
                                'CHECKED_OUT'=> 'gray',
                                'CANCELLED'  => 'red',
                                'NO_SHOW'    => 'orange',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono font-medium text-brand-600 dark:text-brand-400 whitespace-nowrap">
                                {{ $res->reference_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 font-semibold text-xs">
                                        {{ strtoupper(substr($res->guest->first_name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $res->guest->first_name ?? '' }} {{ $res->guest->last_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-400">{{ $res->guest->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $res->room->room_number ?? 'TBD' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($res->check_in_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($res->check_out_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :color="$statusColors[$res->status] ?? 'gray'" :text="str_replace('_', ' ', $res->status)" />
                            </td>
                            <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                                <a href="{{ route('reservations.show', $res) }}" class="font-medium text-brand-600 dark:text-brand-400 hover:text-brand-700 transition-colors">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <p class="text-sm">No reservations found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
