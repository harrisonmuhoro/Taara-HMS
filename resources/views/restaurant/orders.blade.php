<x-app-layout title="Restaurant Orders">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Order History</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View past POS orders.</p>
    </x-slot>

    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</div>
                                <div class="text-xs text-slate-500">By: {{ $order->creator->name ?? 'System' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->type === 'room_charge')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/20">
                                        Room Charge
                                    </span>
                                    <div class="text-xs text-slate-500 mt-1">Rm {{ $order->reservation->room->room_number ?? '?' }} - {{ $order->reservation->guest->full_name ?? '' }}</div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-500/10 dark:ring-slate-400/20">
                                        Walk-in ({{ ucfirst($order->payment_method) }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                <ul class="list-disc list-inside">
                                @foreach($order->items->take(3) as $item)
                                    <li>{{ $item->quantity }}x {{ $item->name }}</li>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <li class="text-xs italic">+ {{ $order->items->count() - 3 }} more</li>
                                @endif
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-brand-600 dark:text-brand-400">
                                ${{ number_format($order->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
