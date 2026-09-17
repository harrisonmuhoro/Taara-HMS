<x-app-layout title="Inventory Report">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Inventory Report</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Stock valuation and alerts.</p>
    </x-slot>

    @include('reports._nav')
    <div class="mb-6 flex justify-end gap-3 print:hidden"><button type="button" onclick="window.print()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Print</button><a href="{{ route('reports.export.queue', ['type' => 'inventory']) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Queue CSV</a><a href="{{ route('reports.inventory.export') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Export CSV</a><a href="{{ route('reports.inventory.export.pdf') }}" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Export PDF</a></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-1 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col shadow-sm">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Inventory Valuation</h3>
            <div class="text-4xl font-bold text-slate-900 dark:text-white mt-4 mb-2">${{ number_format($totalValuation, 2) }}</div>
            <p class="text-xs text-slate-500">Based on current stock levels and cost prices.</p>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-slate-800/60 rounded-2xl border border-rose-200 dark:border-rose-900/50 overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-rose-100 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-900/10 flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <h2 class="text-lg font-semibold text-rose-900 dark:text-rose-400">Low Stock Alerts ({{ $lowStockProducts->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/40">
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                        @forelse($lowStockProducts as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                    {{ $product->name }} <span class="text-slate-400 font-normal">({{ $product->sku }})</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-rose-600 dark:text-rose-400">
                                    {{ $product->current_stock }} {{ $product->unit_measure }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $product->reorder_level }} {{ $product->unit_measure }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-500">All products are adequately stocked.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
