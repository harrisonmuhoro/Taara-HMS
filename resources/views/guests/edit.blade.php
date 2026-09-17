<x-app-layout title="Edit Guest">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Guests', 'url' => route('guests.index')],
            ['label' => 'Edit Guest', 'url' => '#'],
        ]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Edit guest</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update {{ $guest->first_name }} {{ $guest->last_name }}'s profile.</p>
    </x-slot>

    @if ($errors->any())
        <x-alert type="danger" title="Please fix the following errors:" class="mb-6">
            <ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('guests.update', $guest) }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')
        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700/50 dark:bg-slate-800/60">
            <h2 class="mb-5 text-base font-semibold text-slate-900 dark:text-white">Personal information</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ([
                    ['first_name', 'First name', 'text', true], ['last_name', 'Last name', 'text', true],
                    ['email', 'Email address', 'email', true], ['phone', 'Phone number', 'tel', true],
                    ['nationality', 'Nationality', 'text', false],
                    ['id_type', 'ID type', 'text', false], ['id_number', 'ID number', 'text', false],
                    ['address', 'Address', 'text', false], ['city', 'City', 'text', false], ['country', 'Country', 'text', false],
                ] as [$name, $label, $type, $required])
                    <div class="{{ $name === 'address' ? 'sm:col-span-2' : '' }}">
                        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                        <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $guest->{$name}) }}" @required($required)
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 transition-all focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100 @error($name) border-red-500 @enderror">
                        <x-input-error :messages="$errors->get($name)" class="mt-2" />
                    </div>
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-primary-button>Save guest</x-primary-button>
            <a href="{{ route('guests.show', $guest) }}" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">Cancel</a>
        </div>
    </form>
</x-app-layout>
