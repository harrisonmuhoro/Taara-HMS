<x-app-layout title="Search">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Search', 'url' => route('search', ['q' => $term])]]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Search results</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Results matching “{{ $term }}”.</p>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ([
            ['Guests', $guests, fn ($item) => route('guests.show', $item), fn ($item) => $item->first_name.' '.$item->last_name, fn ($item) => $item->email],
            ['Reservations', $reservations, fn ($item) => route('reservations.show', $item), fn ($item) => $item->reservation_number, fn ($item) => ($item->guest?->first_name.' '.$item->guest?->last_name).' · '.$item->status],
            ['Rooms', $rooms, fn ($item) => route('rooms.show', $item), fn ($item) => 'Room '.$item->room_number, fn ($item) => ($item->roomType?->name ?? 'Room').' · '.$item->operational_status],
            ['Staff', $employees, fn ($item) => route('staff.edit', $item), fn ($item) => $item->first_name.' '.$item->last_name, fn ($item) => $item->employee_number.' · '.$item->position],
            ['Invoices', $invoices, fn ($item) => route('finance.invoices.show', $item), fn ($item) => $item->invoice_number, fn ($item) => ($item->guest?->first_name.' '.$item->guest?->last_name).' · '.$item->status],
            ['Products', $products, fn () => route('inventory.products.index'), fn ($item) => $item->name, fn ($item) => $item->sku.' · '.$item->current_stock.' in stock'],
            ['Maintenance', $maintenance, fn ($item) => route('maintenance.show', $item), fn ($item) => $item->title, fn ($item) => 'Room '.($item->room?->room_number ?? 'N/A').' · '.$item->status],
        ] as [$title, $items, $url, $heading, $detail])
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
