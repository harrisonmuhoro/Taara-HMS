<x-app-layout title="Create Purchase Order">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Inventory', 'url' => route('inventory.products.index')], ['label' => 'Purchases', 'url' => route('inventory.purchases.index')], ['label' => 'Create PO', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Create Purchase Order</h1>
    </x-slot>

    @include('inventory._nav')

    <div class="max-w-5xl" x-data="purchaseForm()">
        <form method="POST" action="{{ route('inventory.purchases.store') }}">
            @csrf

            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Order Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="supplier_id" value="Supplier *" />
                        <select id="supplier_id" name="supplier_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('supplier_id')" />
                    </div>

                    <div>
                        <x-input-label for="purchase_date" value="Purchase Date *" />
                        <x-text-input id="purchase_date" name="purchase_date" type="date" class="mt-1 block w-full" :value="old('purchase_date', date('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('purchase_date')" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Order Items</h2>
                    <button type="button" @click="addItem()" class="px-3 py-1.5 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 text-sm font-medium rounded-lg hover:bg-brand-100 dark:hover:bg-brand-900/50 transition-colors">
                        + Add Item
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/40 text-left">
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-32">Quantity</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-40">Unit Cost</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-32 text-right">Total</th>
                                <th class="px-4 py-3 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-3">
                                        <select x-model="item.product_id" :name="`items[${index}][product_id]`" class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm text-sm" required>
                                            <option value="">Select Product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} (In Stock: {{ $product->current_stock }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" min="1" class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm text-sm" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" step="0.01" x-model="item.unit_cost" :name="`items[${index}][unit_cost]`" min="0" class="block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm text-sm" required>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-slate-900 dark:text-white" x-text="(item.quantity * item.unit_cost).toFixed(2)">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-semibold text-slate-900 dark:text-white">Subtotal:</td>
                                <td class="px-4 py-4 text-right font-bold text-lg text-slate-900 dark:text-white" x-text="calculateTotal().toFixed(2)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                <a href="{{ route('inventory.purchases.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                    Submit Purchase Order
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseForm', () => ({
                items: [{ product_id: '', quantity: 1, unit_cost: 0 }],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                calculateTotal() {
                    return this.items.reduce((total, item) => {
                        return total + (parseFloat(item.quantity || 0) * parseFloat(item.unit_cost || 0));
                    }, 0);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
