<x-app-layout title="Settings">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Settings', 'url' => route('settings.index')]]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">System Settings</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure operating defaults for your branch.</p>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if ($errors->any())
        <x-alert type="danger" class="mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    @if (auth()->user()->isSuperAdmin())
        <section class="mb-6 max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700/50 dark:bg-slate-800/60">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Hotel branches</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create and manage the locations available to your hotel.</p>
                </div>
                <span class="text-xs font-medium uppercase tracking-wide text-brand-600 dark:text-brand-400">Super Administrator</span>
            </div>

            @if ($branches->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($branches as $branch)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700 dark:bg-slate-900/70 dark:text-slate-300">{{ $branch->name }} · {{ $branch->code }}</span>
                    @endforeach
                </div>
            @endif

            @can('settings.manage')
                <form method="POST" action="{{ route('configuration.branches.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div>
                        <label for="branch_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Branch name</label>
                        <input id="branch_name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="branch_code" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Branch code</label>
                        <input id="branch_code" name="code" type="text" value="{{ old('code') }}" required maxlength="20" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm uppercase text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="branch_address" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Address</label>
                        <input id="branch_address" name="address" type="text" value="{{ old('address') }}" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="branch_phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                        <input id="branch_phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="30" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="branch_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <input id="branch_email" name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                    <div class="flex items-end sm:justify-end"><button type="submit" class="w-full rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 sm:w-auto">Add branch</button></div>
                </form>
            @endcan
        </section>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700/50 dark:bg-slate-800/60">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Hotel operations</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">These defaults are used for reservations and guest stays.</p>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                @foreach (['check_in_time', 'check_out_time'] as $key)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $definitions[$key]['label'] }}</label>
                        <input id="{{ $key }}" name="{{ $key }}" type="time" value="{{ old($key, $settings[$key]) }}" required
                               class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700/50 dark:bg-slate-800/60">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Regional and financial defaults</h2>
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Currency</label>
                    <input id="currency" name="currency" type="text" maxlength="3" value="{{ old('currency', $settings['currency']) }}" required
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm uppercase text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div>
                    <label for="timezone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Timezone</label>
                    <input id="timezone" name="timezone" type="text" value="{{ old('timezone', $settings['timezone']) }}" required
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tax rate (%)</label>
                    <input id="tax_rate" name="tax_rate" type="number" min="0" max="100" step="0.01" value="{{ old('tax_rate', $settings['tax_rate']) }}" required
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
            </div>
        </section>

        @can('settings.manage')
            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500">Save settings</button>
            </div>
        @endcan
    </form>
</x-app-layout>
