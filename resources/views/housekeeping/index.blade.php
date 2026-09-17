<x-app-layout title="Housekeeping">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Housekeeping', 'url' => route('housekeeping.index')]]" />
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Housekeeping</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Manage daily room cleaning and maintenance statuses.
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('housekeeping.index') }}" class="flex flex-wrap gap-4 items-end">
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
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status Filter</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="DIRTY" {{ request('status') === 'DIRTY' ? 'selected' : '' }}>Dirty</option>
                    <option value="CLEANING" {{ request('status') === 'CLEANING' ? 'selected' : '' }}>Cleaning</option>
                    <option value="CLEAN" {{ request('status') === 'CLEAN' ? 'selected' : '' }}>Clean</option>
                    <option value="INSPECTED" {{ request('status') === 'INSPECTED' ? 'selected' : '' }}>Inspected</option>
                </select>
            </div>

            @if(request()->hasAny(['branch_id', 'status']))
                <a href="{{ route('housekeeping.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Room List --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Room</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type / Floor</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Current Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center font-bold font-mono text-brand-600 dark:text-brand-400">
                                        {{ $room->room_number }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $room->roomType->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Floor {{ $room->floor->floor_number ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $sColors = [
                                        'DIRTY' => 'red', 'CLEANING' => 'yellow',
                                        'CLEAN' => 'green', 'INSPECTED' => 'blue'
                                    ];
                                @endphp
                                <x-status-badge :color="$sColors[$room->housekeeping_status] ?? 'gray'" :text="str_replace('_', ' ', $room->housekeeping_status)" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <form method="POST" action="{{ route('housekeeping.status.update', $room) }}" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="py-1.5 pl-3 pr-8 text-sm bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                        <option value="DIRTY" {{ $room->housekeeping_status == 'DIRTY' ? 'selected' : '' }}>Dirty</option>
                                        <option value="CLEANING" {{ $room->housekeeping_status == 'CLEANING' ? 'selected' : '' }}>Cleaning</option>
                                        <option value="CLEAN" {{ $room->housekeeping_status == 'CLEAN' ? 'selected' : '' }}>Clean</option>
                                        <option value="INSPECTED" {{ $room->housekeeping_status == 'INSPECTED' ? 'selected' : '' }}>Inspected</option>
                                    </select>
                                    <button type="submit" class="px-3 py-1.5 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-lg text-sm font-medium transition-colors">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 text-sm">
                                No rooms found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rooms->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
