<x-app-layout title="Room Board">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Rooms', 'url' => route('rooms.index')]]" />
                <h1 class="mt-2 text-2xl font-semibold text-ink dark:text-[#F0E6D8]">Room board</h1>
                <p class="mt-1 text-sm text-ink-muted">What each room is doing right now.</p>
            </div>
            
            {{-- Room Stats Summary --}}
            <div class="flex items-center gap-4 bg-white dark:bg-slate-800/60 p-2 rounded-xl border border-slate-200 dark:border-slate-700/50">
                <div class="px-3 text-center">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Available</p>
                    <p class="text-xl font-semibold tabular-nums text-olive-700 dark:text-olive-300">{{ $stats['available'] }}</p>
                </div>
                <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                <div class="px-3 text-center">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Occupied</p>
                    <p class="text-xl font-semibold tabular-nums text-brand-600">{{ $stats['occupied'] }}</p>
                </div>
                <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                <div class="px-3 text-center">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Cleaning</p>
                    <p class="text-xl font-bold text-amber-500 dark:text-amber-400">{{ $stats['cleaning'] }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('rooms.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Branch</label>
                <select name="branch_id" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="AVAILABLE" {{ request('status') === 'AVAILABLE' ? 'selected' : '' }}>Available</option>
                    <option value="RESERVED" {{ request('status') === 'RESERVED' ? 'selected' : '' }}>Reserved</option>
                    <option value="OCCUPIED" {{ request('status') === 'OCCUPIED' ? 'selected' : '' }}>Occupied</option>
                    <option value="MAINTENANCE" {{ request('status') === 'MAINTENANCE' ? 'selected' : '' }}>Maintenance</option>
                    <option value="OUT_OF_ORDER" {{ request('status') === 'OUT_OF_ORDER' ? 'selected' : '' }}>Out of Order</option>
                </select>
            </div>
            
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Room Type</label>
                <select name="room_type_id" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Types</option>
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request()->hasAny(['branch_id', 'status', 'room_type_id']))
                <a href="{{ route('rooms.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Room Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse ($rooms as $room)
            @php
                $statusConfig = [
                    'AVAILABLE'    => ['bg' => 'bg-olive-50 dark:bg-olive-900/20', 'border' => 'border-olive-200 dark:border-olive-800', 'text' => 'text-olive-800 dark:text-olive-300', 'dot' => 'bg-olive-600'],
                    'OCCUPIED'     => ['bg' => 'bg-brand-50 dark:bg-brand-950/40', 'border' => 'border-brand-200 dark:border-brand-800', 'text' => 'text-brand-800 dark:text-brand-300', 'dot' => 'bg-brand-500'],
                    'CLEANING'     => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'border' => 'border-amber-200 dark:border-amber-800', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                    'MAINTENANCE'  => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-400', 'dot' => 'bg-orange-500'],
                    'OUT_OF_ORDER' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500'],
                ];
                $conf = $statusConfig[$room->operational_status] ?? ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'];
            @endphp
            
            <a href="{{ route('rooms.show', $room) }}" class="relative block rounded border p-3 {{ $conf['border'] }} {{ $conf['bg'] }}">
                {{-- Status Dot --}}
                <div class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full {{ $conf['dot'] }} shadow-sm"></div>
                
                <div class="text-center mt-2">
                    <p class="text-2xl font-bold font-mono text-slate-900 dark:text-white">{{ $room->room_number }}</p>
                    <p class="text-xs font-semibold {{ $conf['text'] }} mt-1 uppercase tracking-wider">{{ str_replace('_', ' ', $room->operational_status) }}</p>
                    
                    <div class="mt-4 pt-3 border-t border-black/5 dark:border-white/5">
                        <p class="text-xs text-slate-600 dark:text-slate-400 truncate">{{ $room->roomType->name }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-500 truncate">{{ $room->branch->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500 dark:text-slate-400">
                <p>No rooms found matching the selected criteria.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
