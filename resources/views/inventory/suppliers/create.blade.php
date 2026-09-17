<x-app-layout title="Add Supplier">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Inventory', 'url' => route('inventory.products.index')], ['label' => 'Suppliers', 'url' => route('inventory.suppliers.index')], ['label' => 'Add Supplier', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Add New Supplier</h1>
    </x-slot>

    @include('inventory._nav')

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
            <form method="POST" action="{{ route('inventory.suppliers.store') }}">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Company / Supplier Name *" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="contact_person" value="Contact Person Name" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Phone Number" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email Address" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="tax_number" value="Tax/VAT Number" />
                            <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full font-mono uppercase" :value="old('tax_number')" />
                            <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="address" value="Physical Address" />
                            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">{{ old('address') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                    <a href="{{ route('inventory.suppliers.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        Create Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
