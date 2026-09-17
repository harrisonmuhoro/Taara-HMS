<x-app-layout title="Edit Reservation">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Reservations', 'url' => route('reservations.index')],
            ['label' => $reservation->reservation_number, 'url' => route('reservations.show', $reservation)],
            ['label' => 'Edit', 'url' => '#'],
        ]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Edit Reservation</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update the dates and guest count for {{ $reservation->reservation_number }}.</p>
    </x-slot>

    @if ($errors->any())
        <x-alert type="danger" class="mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700/50 dark:bg-slate-800/60">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Reservation details</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}
                · Room {{ $reservation->room->room_number ?? 'Unassigned' }}
            </p>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="check_in_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Check-in date</label>
                    <input id="check_in_date" name="check_in_date" type="date" min="{{ today()->toDateString() }}" required
                           value="{{ old('check_in_date', $reservation->check_in_date->toDateString()) }}"
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div>
                    <label for="check_out_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Check-out date</label>
                    <input id="check_out_date" name="check_out_date" type="date" required
                           value="{{ old('check_out_date', $reservation->check_out_date->toDateString()) }}"
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div>
                    <label for="adults" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Adults</label>
                    <input id="adults" name="adults" type="number" min="1" max="10" required
                           value="{{ old('adults', $reservation->adults) }}"
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
                <div>
                    <label for="children" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Children</label>
                    <input id="children" name="children" type="number" min="0" max="10"
                           value="{{ old('children', $reservation->children) }}"
                           class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                </div>
            </div>

            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                <textarea id="notes" name="notes" rows="4"
                          class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-900 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">{{ old('notes', $reservation->special_requests) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('reservations.show', $reservation) }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">Cancel</a>
            <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Save changes</button>
        </div>
    </form>
</x-app-layout>
