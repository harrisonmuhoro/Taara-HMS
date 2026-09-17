<x-app-layout title="Add Product">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Inventory', 'url' => route('inventory.products.index')], ['label' => 'Products', 'url' => route('inventory.products.index')], ['label' => 'Add Product', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Add New Product</h1>
    </x-slot>

    @include('inventory._nav')

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
            <form method="POST" action="{{ route('inventory.products.store') }}">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Product Name *" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="sku" value="SKU (Leave blank to auto-generate)" />
                            <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full font-mono uppercase" :value="old('sku')" />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" value="Category *" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div>
                            <x-input-label for="cost_price" value="Cost Price *" />
                            <x-text-input id="cost_price" name="cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('cost_price')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('cost_price')" />
                        </div>

                        <div>
                            <x-input-label for="selling_price" value="Selling Price *" />
                            <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('selling_price')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('selling_price')" />
                        </div>

                        <div>
                            <x-input-label for="unit" value="Unit of Measurement *" />
                            <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" :value="old('unit', 'pcs')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                        </div>

                        <div>
                            <x-input-label for="reorder_level" value="Low Stock Alert Level *" />
                            <x-text-input id="reorder_level" name="reorder_level" type="number" min="0" class="mt-1 block w-full" :value="old('reorder_level', 10)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('reorder_level')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                    <a href="{{ route('inventory.products.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
