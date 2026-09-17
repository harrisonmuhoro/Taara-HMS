<x-app-layout title="Purchases">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Purchase Orders</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage procurement, orders, and receiving.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @can('inventory.purchase')
                    <a href="{{ route('inventory.purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Create Purchase Order
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    @include('inventory._nav')

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6 flex flex-wrap gap-4 items-end">
        <form method="GET" action="{{ route('inventory.purchases.index') }}" class="flex-1 flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input-label for="search" value="Search PO Number" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all placeholder:text-slate-400" placeholder="e.g. PO-202610...">
                </div>
            </div>
            <div class="min-w-[150px]">
                <x-input-label for="status" value="Status" />
                <select name="status" id="status" onchange="this.form.submit()" class="mt-1 block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                    <option value="ORDERED" {{ request('status') === 'ORDERED' ? 'selected' : '' }}>Ordered</option>
                    <option value="RECEIVED" {{ request('status') === 'RECEIVED' ? 'selected' : '' }}>Received</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('inventory.purchases.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Clear</a>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">PO Number</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($purchases as $purchase)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-brand-600 dark:text-brand-400">
                                <a href="{{ route('inventory.purchases.show', $purchase) }}" class="hover:underline">
                                    {{ $purchase->purchase_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $purchase->supplier->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white font-medium text-right">
                                {{ number_format($purchase->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'DRAFT' => 'slate',
                                        'ORDERED' => 'blue',
                                        'RECEIVED' => 'emerald',
                                        'CANCELLED' => 'rose'
                                    ];
                                @endphp
                                <x-status-badge :color="$statusColors[$purchase->status] ?? 'gray'" :text="$purchase->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.purchases.show', $purchase) }}" class="text-slate-400 hover:text-brand-600 transition-colors">
                                    View details &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No purchase orders found</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a new purchase order to start stocking inventory.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($purchases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
