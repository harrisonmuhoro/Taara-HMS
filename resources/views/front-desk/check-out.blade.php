<x-app-layout title="Front Desk Check-out">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Front Desk Check-out', 'url' => route('front-desk.check-out')]]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Front Desk Check-out</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Active stays ready to be checked out after their balance is settled.</p>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700/50 dark:bg-slate-800/60">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead class="bg-slate-50 dark:bg-slate-800/40">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Stay</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Checked in</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($stays as $stay)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">#{{ $stay->id }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $stay->guest->first_name }} {{ $stay->guest->last_name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">Room {{ $stay->room->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $stay->actual_check_in->format('M j, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('front-desk.check-out.process', $stay) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Check out</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">No active stays are currently available for check-out.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($stays->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-700/50">{{ $stays->links() }}</div>
        @endif
    </div>
</x-app-layout>
