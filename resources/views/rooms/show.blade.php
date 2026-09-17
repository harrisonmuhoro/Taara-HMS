<x-app-layout :title="'Room ' . $room->room_number">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Rooms', 'url' => route('rooms.index')],
            ['label' => 'Room ' . $room->room_number, 'url' => '#'],
        ]" />
        
        <div class="mt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Room {{ $room->room_number }}</h1>
                @php
                    $statusConfig = [
                        'AVAILABLE'    => 'green',
                        'OCCUPIED'     => 'blue',
                        'CLEANING'     => 'yellow',
                        'MAINTENANCE'  => 'orange',
                        'OUT_OF_ORDER' => 'red',
                    ];
                    $color = $statusConfig[$room->operational_status] ?? 'gray';
                @endphp
                <x-status-badge :color="$color" :text="str_replace('_', ' ', $room->operational_status)" />
            </div>
            
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('reservations.create', ['room_id' => $room->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Book Room
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Details --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Room Information</h2>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
                        <dt class="text-slate-500 dark:text-slate-400">Type</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $room->roomType->name }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
                        <dt class="text-slate-500 dark:text-slate-400">Branch</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $room->branch->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
                        <dt class="text-slate-500 dark:text-slate-400">Floor</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $room->floor->floor_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between pb-1">
                        <dt class="text-slate-500 dark:text-slate-400">Base Rate</dt>
                        <dd class="font-medium text-brand-600 dark:text-brand-400">KES {{ number_format($room->roomType->base_rate ?? 0, 2) }}</dd>
                    </div>
                </dl>
            </div>
            
            {{-- Room Amenities/Features --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Features</h2>
                <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Max Occupancy: {{ $room->roomType->max_occupancy ?? 2 }} adults
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        Daily Cleaning Included
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        High-Speed Wi-Fi
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right Column: Reservations --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Active & Upcoming Reservations</h2>
                </div>
                
                @if ($reservations->isEmpty())
                    <div class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <p class="text-sm">No active or upcoming reservations for this room.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-700/30">
                        @foreach ($reservations as $res)
                            <div class="p-5 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">
                                        <a href="{{ route('reservations.show', $res) }}" class="hover:underline">
                                            {{ $res->guest->first_name }} {{ $res->guest->last_name }}
                                        </a>
                                    </p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                        {{ \Carbon\Carbon::parse($res->check_in_date)->format('M j') }} — {{ \Carbon\Carbon::parse($res->check_out_date)->format('M j, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $sColors = ['PENDING' => 'yellow', 'CONFIRMED' => 'blue', 'CHECKED_IN' => 'green'];
                                        $sColor = $sColors[$res->status] ?? 'gray';
                                    @endphp
                                    <x-status-badge :color="$sColor" :text="str_replace('_', ' ', $res->status)" />
                                    <p class="text-xs text-slate-400 mt-2">{{ $res->reservation_number }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</x-app-layout>
