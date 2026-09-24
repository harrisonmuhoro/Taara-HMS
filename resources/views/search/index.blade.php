<x-app-layout title="Search">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Search', 'url' => route('search', ['q' => $term])]]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Search results</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Results matching “{{ $term }}”.</p>
    </x-slot>

    <form method="GET" action="{{ route('search') }}" class="mb-6 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800/60 sm:flex-row sm:items-end">
        <input type="hidden" name="q" value="{{ $term }}">
        <div class="flex-1">
            <label for="search-section" class="form-label">Search in</label>
            <select id="search-section" name="section" class="form-input">
                @foreach (['quick' => 'Quick search (guests, reservations, rooms)', 'all' => 'All categories', 'guests' => 'Guests', 'reservations' => 'Reservations', 'rooms' => 'Rooms', 'staff' => 'Staff', 'invoices' => 'Invoices', 'products' => 'Products', 'maintenance' => 'Maintenance'] as $value => $label)
                    <option value="{{ $value }}" @selected($section === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary justify-center">Search category</button>
    </form>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ([
            ['guests', 'Guests', $guests, fn ($item) => route('guests.show', $item), fn ($item) => $item->first_name.' '.$item->last_name, fn ($item) => $item->email],
            ['reservations', 'Reservations', $reservations, fn ($item) => route('reservations.show', $item), fn ($item) => $item->reservation_number, fn ($item) => ($item->guest?->first_name.' '.$item->guest?->last_name).' · '.$item->status],
            ['rooms', 'Rooms', $rooms, fn ($item) => route('rooms.show', $item), fn ($item) => 'Room '.$item->room_number, fn ($item) => ($item->roomType?->name ?? 'Room').' · '.$item->operational_status],
            ['staff', 'Staff', $employees, fn ($item) => route('staff.edit', $item), fn ($item) => $item->first_name.' '.$item->last_name, fn ($item) => $item->employee_number.' · '.$item->position],
            ['invoices', 'Invoices', $invoices, fn ($item) => route('finance.invoices.show', $item), fn ($item) => $item->invoice_number, fn ($item) => ($item->guest?->first_name.' '.$item->guest?->last_name).' · '.$item->status],
            ['products', 'Products', $products, fn () => route('inventory.products.index'), fn ($item) => $item->name, fn ($item) => $item->sku.' · '.$item->current_stock.' in stock'],
            ['maintenance', 'Maintenance', $maintenance, fn ($item) => route('maintenance.show', $item), fn ($item) => $item->title, fn ($item) => 'Room '.($item->room?->room_number ?? 'N/A').' · '.$item->status],
        ] as [$key, $title, $items, $url, $heading, $detail])
            @continue($section !== 'all' && $section !== 'quick' && $section !== $key)
            <x-card title="{{ $title }}" subtitle="{{ $items->count() }} result(s)">
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($items as $item)
                        <a href="{{ $url($item) }}" class="block py-3 first:pt-0 last:pb-0 hover:text-brand-600 dark:hover:text-brand-400"><p class="font-medium text-slate-900 dark:text-white">{{ $heading($item) }}</p><p class="text-sm text-slate-500 dark:text-slate-400">{{ $detail($item) }}</p></a>
                    @empty
                        <p class="py-3 text-sm text-slate-500">No matches found.</p>
                    @endforelse
                </div>
            </x-card>
        @endforeach
    </div>
</x-app-layout>
