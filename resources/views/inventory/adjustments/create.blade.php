<x-app-layout title="Adjust Stock">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Inventory', 'url' => route('inventory.products.index')], ['label' => 'Stock Adjustments', 'url' => route('inventory.adjustments.index')], ['label' => 'New Adjustment', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Manual Stock Adjustment</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manually correct stock for damages, returns, or consumption.</p>
    </x-slot>

    @include('inventory._nav')

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm" x-data="{ quantity: 0, type: 'ADJUSTMENT' }">
            <form method="POST" action="{{ route('inventory.adjustments.store') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <x-input-label for="product_id" value="Product *" />
                        <select id="product_id" name="product_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} — Current Stock: {{ $product->current_stock }} {{ $product->unit }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                    </div>

                    <div>
                        <x-input-label value="Adjustment Type *" />
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            @foreach([
                                'ADJUSTMENT' => ['label' => 'Manual Adjustment', 'desc' => 'Correct a stock discrepancy', 'color' => 'blue'],
                                'DAMAGE'     => ['label' => 'Damage / Write-off', 'desc' => 'Remove damaged/unusable items', 'color' => 'rose'],
                                'CONSUMPTION'=> ['label' => 'Consumption', 'desc' => 'Internal hotel usage', 'color' => 'amber'],
                                'RETURN'     => ['label' => 'Return to Stock', 'desc' => 'Add items back to inventory', 'color' => 'emerald'],
                            ] as $value => $info)
                                <label class="relative flex cursor-pointer rounded-xl border-2 bg-white dark:bg-slate-800 p-4 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 has-[:checked]:ring-2 has-[:checked]:ring-brand-500 has-[:checked]:border-brand-500 dark:border-slate-700 transition-all" x-on:click="type = '{{ $value }}'">
                                    <input type="radio" name="movement_type" value="{{ $value }}" class="sr-only" {{ old('movement_type', 'ADJUSTMENT') === $value ? 'checked' : '' }}>
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white">{{ $info['label'] }}</span>
                                        <span class="block text-xs text-slate-500 mt-0.5">{{ $info['desc'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('movement_type')" />
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity *" />
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-1.5">Use a <strong>positive</strong> number to add stock, a <strong>negative</strong> number to deduct.</p>
                        <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Reason / Notes *" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required placeholder="Explain the reason for this adjustment...">{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                    <a href="{{ route('inventory.adjustments.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        Submit Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
