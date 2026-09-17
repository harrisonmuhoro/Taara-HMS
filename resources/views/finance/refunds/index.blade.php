<x-app-layout title="Refunds">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Finance', 'url' => '#'], ['label' => 'Refunds', 'url' => route('finance.refunds.index')]]" />
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Refunds Ledger</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View and process guest refunds.</p>
            </div>
            <div class="flex items-center gap-3">
                @can('payments.refund')
                    <a href="{{ route('finance.refunds.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                        Process Refund
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    @include('finance._nav')

    {{-- Refunds Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Invoice / Guest</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Processed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($refunds as $refund)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $refund->processed_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('finance.invoices.show', $refund->payment->invoice) }}" class="text-sm font-medium text-slate-900 dark:text-white hover:underline block">
                                    {{ $refund->payment->invoice->invoice_number ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $refund->payment->invoice->guest->first_name ?? '' }} {{ $refund->payment->invoice->guest->last_name ?? '' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                {{ $refund->reason }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600 dark:text-rose-400">
                                - KES {{ number_format($refund->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ str_replace('_', ' ', $refund->refund_method) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $refund->processor->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No refunds found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($refunds->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $refunds->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
