<x-app-layout title="{{ isset($item) ? 'Edit Menu Item' : 'New Menu Item' }}">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ isset($item) ? 'Edit Menu Item: '.$item->name : 'Create New Menu Item' }}</h1>
    </x-slot>

    <div class="max-w-3xl bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
        <form action="{{ isset($item) ? route('restaurant.menu.update', $item) : route('restaurant.menu.store') }}" method="POST">
            @csrf
            @if(isset($item)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Item Name</label>
                    <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500" required>
                    @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Price</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $item->price ?? '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500" required>
                    @error('price') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Prep Time (Mins)</label>
                    <input type="number" name="prep_time_minutes" value="{{ old('prep_time_minutes', $item->prep_time_minutes ?? 0) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div class="col-span-2" x-data="{ newCategory: false }">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Category</label>
                    
                    <div x-show="!newCategory" class="flex gap-2">
                        <select name="menu_category_id" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500" :required="!newCategory">
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('menu_category_id', $item->menu_category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" @click="newCategory = true" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium whitespace-nowrap">New</button>
                    </div>

                    <div x-show="newCategory" class="flex gap-2" style="display: none;">
                        <input type="text" name="category_name" placeholder="New Category Name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500" :required="newCategory" :disabled="!newCategory">
                        <button type="button" @click="newCategory = false" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium whitespace-nowrap">Cancel</button>
                    </div>
                    @error('menu_category_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">{{ old('description', $item->description ?? '') }}</textarea>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', $item->is_available ?? true) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-700 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Item is available for order</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('restaurant.menu.index') }}" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition-colors">Save Item</button>
            </div>
        </form>
    </div>
</x-app-layout>
