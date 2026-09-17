<x-app-layout title="Maintenance">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Maintenance</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage hotel maintenance tickets and assignments.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-4 bg-white dark:bg-slate-800/60 p-2 rounded-xl border border-slate-200 dark:border-slate-700/50">
                    <div class="px-3 text-center">
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Open</p>
                        <p class="text-lg font-bold text-rose-500 dark:text-rose-400">{{ $stats['open'] }}</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                    <div class="px-3 text-center">
                        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">In Progress</p>
                        <p class="text-lg font-bold text-amber-500 dark:text-amber-400">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>

                @can('maintenance.create')
                    <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Report Issue
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('maintenance.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Open</option>
                    <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                    <option value="CLOSED" {{ request('status') === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Priority</label>
                <select name="priority" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Priorities</option>
                    <option value="LOW" {{ request('priority') === 'LOW' ? 'selected' : '' }}>Low</option>
                    <option value="MEDIUM" {{ request('priority') === 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                    <option value="HIGH" {{ request('priority') === 'HIGH' ? 'selected' : '' }}>High</option>
                    <option value="CRITICAL" {{ request('priority') === 'CRITICAL' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            @if(request()->hasAny(['status', 'priority']))
                <a href="{{ route('maintenance.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tickets Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID / Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Issue</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Assigned To</th>
                        <th class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">#TKT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $ticket->reported_at->format('M j, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                    Room {{ $ticket->room->room_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('maintenance.show', $ticket) }}" class="text-sm font-medium text-brand-600 dark:text-brand-400 hover:underline">
                                    {{ $ticket->title }}
                                </a>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $ticket->category->name ?? 'General' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $pColors = ['LOW' => 'slate', 'MEDIUM' => 'blue', 'HIGH' => 'orange', 'CRITICAL' => 'red'];
                                @endphp
                                <x-status-badge :color="$pColors[$ticket->priority] ?? 'gray'" :text="$ticket->priority" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $sColors = ['OPEN' => 'rose', 'IN_PROGRESS' => 'amber', 'RESOLVED' => 'emerald', 'CLOSED' => 'slate', 'CANCELLED' => 'gray'];
                                @endphp
                                <x-status-badge :color="$sColors[$ticket->status] ?? 'gray'" :text="$ticket->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ticket->assignee)
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                            {{ substr($ticket->assignee->first_name ?? $ticket->assignee->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $ticket->assignee->first_name ?? $ticket->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('maintenance.show', $ticket) }}" class="text-slate-400 hover:text-brand-600 transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No maintenance tickets</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Everything is running smoothly.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
