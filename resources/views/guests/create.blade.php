<x-app-layout title="New Guest">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Guests', 'url' => route('guests.index')],
            ['label' => 'New Guest', 'url' => '#'],
        ]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">New Guest</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Fill in the details below to add a new guest profile.</p>
    </x-slot>

    @if ($errors->any())
        <x-alert type="danger" title="Please fix the following errors:" class="mb-6">
            <ul class="list-disc list-inside space-y-1 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('guests.store') }}" class="space-y-6 max-w-4xl">
        @csrf

        {{-- Personal Information --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">1</span>
                Personal Information
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">First Name <span class="text-red-500">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('first_name') border-red-500 @enderror">
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('last_name') border-red-500 @enderror">
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('email') border-red-500 @enderror">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('phone') border-red-500 @enderror">
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <label for="nationality" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nationality</label>
                    <input type="text" id="nationality" name="nationality" value="{{ old('nationality') }}" autocomplete="country-name"
                           placeholder="e.g. Kenyan"
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all @error('nationality') border-red-500 @enderror">
                    <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                </div>
            </div>
        </div>

        {{-- Identification --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center text-brand-600 dark:text-brand-400 text-sm font-bold">2</span>
                Identification & Address
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="id_type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ID Type</label>
                    <select id="id_type" name="id_type"
                            class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                        <option value="">— Select ID Type —</option>
                        <option value="PASSPORT" {{ old('id_type') == 'PASSPORT' ? 'selected' : '' }}>Passport</option>
                        <option value="NATIONAL_ID" {{ old('id_type') == 'NATIONAL_ID' ? 'selected' : '' }}>National ID</option>
                        <option value="DRIVERS_LICENSE" {{ old('id_type') == 'DRIVERS_LICENSE' ? 'selected' : '' }}>Driver's License</option>
                    </select>
                </div>
                <div>
                    <label for="id_number" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">ID Number</label>
                    <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}"
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Country</label>
                    <input type="text" id="country" name="country" value="{{ old('country') }}"
                           class="block w-full py-2.5 px-3 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:-translate-y-0.5">
                Save Guest
            </button>
            <a href="{{ route('guests.index') }}"
               class="px-5 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm font-medium rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</x-app-layout>
