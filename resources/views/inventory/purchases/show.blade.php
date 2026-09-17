<x-app-layout title="Purchase Order {{ $purchase->purchase_number }}">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Inventory', 'url' => route('inventory.products.index')], ['label' => 'Purchases', 'url' => route('inventory.purchases.index')], ['label' => $purchase->purchase_number, 'url' => '']]" />
        
        <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Purchase Order {{ $purchase->purchase_number }}
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Created on {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M j, Y') }} by {{ $purchase->creator->name ?? 'Unknown' }}
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                @php
                    $statusColors = [
                        'DRAFT' => 'slate',
                        'ORDERED' => 'blue',
                        'RECEIVED' => 'emerald',
                        'CANCELLED' => 'rose'
                    ];
                @endphp
                <x-status-badge :color="$statusColors[$purchase->status] ?? 'gray'" :text="$purchase->status" />
            </div>
        </div>
    </x-slot>

    @include('inventory._nav')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Details --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Items --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700/50">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Order Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/40">
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Unit Cost</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                            @foreach($purchase->items as $item)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->product->name ?? 'Deleted Product' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">
                                        {{ number_format($item->unit_cost, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                        {{ number_format($item->total_amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 dark:bg-slate-800/40">
                                <td colspan="3" class="px-6 py-4 text-right font-semibold text-slate-900 dark:text-white text-sm uppercase">Total Amount:</td>
                                <td class="px-6 py-4 text-right font-bold text-lg text-brand-600 dark:text-brand-400">
                                    {{ number_format($purchase->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Supplier & Status --}}
        <div class="space-y-6">
            
            {{-- Supplier Info --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4 uppercase tracking-wider">Supplier Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Company Name</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $purchase->supplier->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Contact Person</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $purchase->supplier->contact_person ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Email / Phone</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $purchase->supplier->email ?? 'No Email' }}</p>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $purchase->supplier->phone ?? 'No Phone' }}</p>
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            @can('update', $purchase)
                @if($purchase->status !== 'RECEIVED' && $purchase->status !== 'CANCELLED')
                    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4 uppercase tracking-wider">Update Status</h3>
                        
                        <form method="POST" action="{{ route('inventory.purchases.updateStatus', $purchase) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="status" value="Change Status To" />
                                    <select id="status" name="status" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                                        @if($purchase->status === 'DRAFT')
                                            <option value="ORDERED">Mark as Ordered</option>
                                        @endif
                                        <option value="RECEIVED">Mark as Received</option>
                                        <option value="CANCELLED">Cancel Order</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                                
                                <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-xl p-4 flex gap-3 text-amber-800 dark:text-amber-300">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <p class="text-sm">Marking as <strong>Received</strong> will automatically update the inventory stock levels for all products in this order. This action cannot be reversed.</p>
                                </div>

                                <button type="submit" class="w-full px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endcan
        </div>
    </div>
</x-app-layout>
