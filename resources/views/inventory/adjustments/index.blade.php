<x-app-layout title="Stock Adjustments">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Stock Adjustments</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View inventory movements and manually adjust stock.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @can('inventory.adjust')
                    <a href="{{ route('inventory.adjustments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Adjust Stock
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    @include('inventory._nav')

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6 flex flex-wrap gap-4 items-end">
        <form method="GET" action="{{ route('inventory.adjustments.index') }}" class="flex-1 flex flex-wrap gap-4">
            <div class="min-w-[200px]">
                <x-input-label for="type" value="Movement Type" />
                <select name="type" id="type" onchange="this.form.submit()" class="mt-1 block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Types</option>
                    <option value="PURCHASE" {{ request('type') === 'PURCHASE' ? 'selected' : '' }}>Purchase</option>
                    <option value="ADJUSTMENT" {{ request('type') === 'ADJUSTMENT' ? 'selected' : '' }}>Manual Adjustment</option>
                    <option value="DAMAGE" {{ request('type') === 'DAMAGE' ? 'selected' : '' }}>Damage</option>
                    <option value="CONSUMPTION" {{ request('type') === 'CONSUMPTION' ? 'selected' : '' }}>Consumption</option>
                    <option value="RETURN" {{ request('type') === 'RETURN' ? 'selected' : '' }}>Return</option>
                </select>
            </div>
        </form>
        
        @if(request()->has('type'))
            <a href="{{ route('inventory.adjustments.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Clear</a>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Balance After</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Notes & User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($movements as $movement)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                {{ $movement->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $movement->product->name ?? 'Unknown Product' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">SKU: {{ $movement->product->sku ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                                    {{ $movement->movement_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <span class="text-sm font-bold {{ $movement->quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                {{ $movement->balance_after }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $movement->notes ?? 'No notes' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">By: {{ $movement->creator->name ?? 'System' }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No stock movements found</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">No inventory activity has been recorded yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($movements->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
