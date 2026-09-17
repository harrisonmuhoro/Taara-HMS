<x-app-layout title="Reservations">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Reservations', 'url' => route('reservations.index')]]" />
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Reservations</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Manage all hotel reservations across branches.
                </p>
            </div>
                <a href="{{ route('reservations.calendar') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Calendar
                </a>
                <a href="{{ route('reservations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/30 transition-all duration-200 hover:shadow-brand-500/40 hover:-translate-y-0.5 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Reservation
            </a>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" :dismissible="true" class="mb-5">{{ session('error') }}</x-alert>
    @endif

    {{-- ─── Filters ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('reservations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-3 items-end">
            
            {{-- Search --}}
            <div class="sm:col-span-2 lg:flex-1 lg:min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref #, guest name, email…"
                           class="block w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <select name="status" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_', ' ', $s) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Check-in From</label>
                <input type="date" name="check_in_from" value="{{ request('check_in_from') }}"
                       class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Check-in To</label>
                <input type="date" name="check_in_to" value="{{ request('check_in_to') }}"
                       class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search','status','check_in_from','check_in_to']))
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ─── Reservations Table ──────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700/50">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-slate-900 dark:text-white">{{ $reservations->total() }}</span> reservations found
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ref #</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Room</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check-in</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Check-out</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hide-mobile">Nights</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hide-mobile">Amount</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($reservations as $res)
                        @php
                            $nights = \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date));
                            $statusColors = [
                                'PENDING'    => 'yellow',
                                'CONFIRMED'  => 'blue',
                                'CHECKED_IN' => 'green',
                                'CHECKED_OUT'=> 'gray',
                                'CANCELLED'  => 'red',
                                'NO_SHOW'    => 'orange',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors group">
                            <td class="px-6 py-4 text-sm font-mono font-semibold text-brand-600 dark:text-brand-400 whitespace-nowrap">
                                {{ $res->reservation_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 font-semibold text-xs shrink-0">
                                        {{ strtoupper(substr($res->guest->first_name ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 dark:text-white leading-tight">
                                            {{ $res->guest->first_name ?? '' }} {{ $res->guest->last_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $res->guest->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300 whitespace-nowrap font-medium">
                                {{ $res->room->room_number ?? 'TBD' }}
                                <span class="text-xs text-slate-400 block font-normal">{{ $res->room->roomType->name ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($res->check_in_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($res->check_out_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap text-center hide-mobile">
                                {{ $nights }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white whitespace-nowrap hide-mobile">
                                KES {{ number_format($res->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :color="$statusColors[$res->status] ?? 'gray'" :text="str_replace('_', ' ', $res->status)" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('reservations.show', $res) }}"
                                       class="p-1.5 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg transition-colors"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    @if (in_array($res->status, ['PENDING', 'CONFIRMED']))
                                        <a href="{{ route('reservations.edit', $res) }}"
                                           class="p-1.5 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors"
                                           title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <p class="text-sm font-medium">No reservations found</p>
                                    <p class="text-xs">Try adjusting your filters or <a href="{{ route('reservations.create') }}" class="text-brand-500 hover:underline">create a new reservation</a>.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reservations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
