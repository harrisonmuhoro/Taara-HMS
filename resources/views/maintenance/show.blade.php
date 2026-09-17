<x-app-layout title="Ticket #{{ str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) }}">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Maintenance', 'url' => route('maintenance.index')], ['label' => 'Ticket #' . str_pad($maintenance->id, 5, '0', STR_PAD_LEFT), 'url' => '']]" />
        
        <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $maintenance->title }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Reported by {{ $maintenance->reporter->name ?? 'Unknown' }} on {{ $maintenance->reported_at->format('M j, Y, g:i A') }}
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                @php
                    $sColors = ['OPEN' => 'rose', 'IN_PROGRESS' => 'amber', 'RESOLVED' => 'emerald', 'CLOSED' => 'slate', 'CANCELLED' => 'gray'];
                    $pColors = ['LOW' => 'slate', 'MEDIUM' => 'blue', 'HIGH' => 'orange', 'CRITICAL' => 'red'];
                @endphp
                <x-status-badge :color="$sColors[$maintenance->status] ?? 'gray'" :text="'Status: ' . $maintenance->status" />
                <x-status-badge :color="$pColors[$maintenance->priority] ?? 'gray'" :text="'Priority: ' . $maintenance->priority" />
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Details & Update Form --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Ticket Details Card --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Issue Description</h3>
                <div class="prose dark:prose-invert max-w-none text-sm text-slate-700 dark:text-slate-300">
                    {!! nl2br(e($maintenance->description)) !!}
                </div>
                
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-200 dark:border-slate-700/50">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Room</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $maintenance->room->room_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Category</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $maintenance->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Assigned To</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $maintenance->assignee->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Estimated Cost</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $maintenance->estimated_cost ? 'KES ' . number_format($maintenance->estimated_cost, 2) : '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Comments Section --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm flex flex-col h-[500px]">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Updates & Comments</h3>
                
                <div class="flex-1 overflow-y-auto space-y-4 pr-2 mb-4 custom-scrollbar">
                    @forelse($maintenance->comments as $comment)
                        <div class="flex gap-4 {{ $comment->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                            <div class="h-8 w-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex-shrink-0 flex items-center justify-center text-xs font-bold text-brand-600 dark:text-brand-400">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="max-w-[80%]">
                                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-3 {{ $comment->user_id === auth()->id() ? 'rounded-tr-none bg-brand-50 dark:bg-brand-900/10' : 'rounded-tl-none' }}">
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{!! nl2br(e($comment->comment)) !!}</p>
                                </div>
                                <div class="flex items-center gap-2 mt-1 px-1 {{ $comment->user_id === auth()->id() ? 'justify-end' : '' }}">
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $comment->user->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center">
                            <p class="text-sm text-slate-500 italic">No updates or comments yet.</p>
                        </div>
                    @endforelse
                </div>

                @can('update', $maintenance)
                    <form action="{{ route('maintenance.comments.store', $maintenance) }}" method="POST" class="mt-auto border-t border-slate-200 dark:border-slate-700/50 pt-4">
                        @csrf
                        <div class="flex gap-3">
                            <textarea name="comment" rows="1" class="flex-1 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-xl shadow-sm text-sm resize-none py-2.5" placeholder="Write an update..." required></textarea>
                            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl shadow-sm transition-colors self-end">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                            </button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>

        {{-- Right Column: Update Status Form --}}
        <div class="space-y-6">
            @can('update', $maintenance)
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Update Ticket</h3>
                    
                    <form method="POST" action="{{ route('maintenance.update', $maintenance) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                    <option value="OPEN" {{ $maintenance->status === 'OPEN' ? 'selected' : '' }}>Open</option>
                                    <option value="IN_PROGRESS" {{ $maintenance->status === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                    <option value="RESOLVED" {{ $maintenance->status === 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                                    <option value="CLOSED" {{ $maintenance->status === 'CLOSED' ? 'selected' : '' }}>Closed</option>
                                    <option value="CANCELLED" {{ $maintenance->status === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>

                            <div>
                                <x-input-label for="assigned_to" value="Assign To" />
                                <select id="assigned_to" name="assigned_to" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($maintenanceStaff as $staff)
                                        <option value="{{ $staff->id }}" {{ $maintenance->assigned_to === $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('assigned_to')" />
                            </div>

                            <div>
                                <x-input-label for="estimated_cost" value="Estimated Cost (KES)" />
                                <x-text-input id="estimated_cost" name="estimated_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('estimated_cost', $maintenance->estimated_cost)" />
                                <x-input-error class="mt-2" :messages="$errors->get('estimated_cost')" />
                            </div>

                            <div>
                                <x-input-label for="actual_cost" value="Actual Cost (KES)" />
                                <x-text-input id="actual_cost" name="actual_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('actual_cost', $maintenance->actual_cost)" />
                                <x-input-error class="mt-2" :messages="$errors->get('actual_cost')" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                                Save Updates
                            </button>
                        </div>
                    </form>
                </div>
            @endcan

            {{-- Timeline --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4 uppercase tracking-wider">Timeline</h3>
                
                <div class="relative border-l border-slate-200 dark:border-slate-700 ml-3 space-y-4">
                    <div class="relative pl-6">
                        <div class="absolute -left-1.5 top-1.5 h-3 w-3 rounded-full bg-slate-400 ring-4 ring-white dark:ring-slate-800"></div>
                        <p class="text-xs font-semibold text-slate-900 dark:text-white">Reported</p>
                        <p class="text-xs text-slate-500">{{ $maintenance->reported_at->format('M j, Y, g:i A') }}</p>
                    </div>
                    
                    @if($maintenance->resolved_at)
                        <div class="relative pl-6">
                            <div class="absolute -left-1.5 top-1.5 h-3 w-3 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-slate-800"></div>
                            <p class="text-xs font-semibold text-slate-900 dark:text-white">Resolved</p>
                            <p class="text-xs text-slate-500">{{ $maintenance->resolved_at->format('M j, Y, g:i A') }}</p>
                        </div>
                    @endif

                    @if($maintenance->closed_at)
                        <div class="relative pl-6">
                            <div class="absolute -left-1.5 top-1.5 h-3 w-3 rounded-full bg-slate-900 dark:bg-slate-400 ring-4 ring-white dark:ring-slate-800"></div>
                            <p class="text-xs font-semibold text-slate-900 dark:text-white">Closed</p>
                            <p class="text-xs text-slate-500">{{ $maintenance->closed_at->format('M j, Y, g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
