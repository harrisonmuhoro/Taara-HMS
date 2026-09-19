<x-app-layout title="Front Desk Check-out">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Check-out', 'url' => route('front-desk.check-out')]]" />
        <h1 class="mt-2 text-2xl font-semibold text-ink dark:text-[#F0E6D8]">Check-out</h1>
        <p class="mt-1 text-sm text-ink-muted">Active stays. Settle the bill before they leave.</p>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>
    @endif

    <div class="overflow-hidden rounded border border-[#D9CFC0] bg-surface dark:border-[#3A3228] dark:bg-surface-dark">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Stay</th>
                        <th>Room</th>
                        <th>Checked in</th>
                        <th class="text-right"> </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stays as $stay)
                        <tr>
                            <td class="font-medium">{{ $stay->guest->first_name }} {{ $stay->guest->last_name }}</td>
                            <td>#{{ $stay->id }}</td>
                            <td>{{ $stay->room->room_number }}</td>
                            <td>{{ $stay->actual_check_in->format('j M Y H:i') }}</td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('front-desk.check-out.process', $stay) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary">Check out</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-ink-muted">No stays ready for check-out.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stays->hasPages())
            <div class="border-t border-[#D9CFC0] px-4 py-3 dark:border-[#3A3228]">{{ $stays->links() }}</div>
        @endif
    </div>
</x-app-layout>
