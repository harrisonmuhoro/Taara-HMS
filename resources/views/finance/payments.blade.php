<x-app-layout title="Payments">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Finance', 'url' => '#'], ['label' => 'Payments', 'url' => route('finance.payments')]]" />
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Payments Registry</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Track and verify all incoming payments and transactions.
                </p>
            </div>
        </div>
    </x-slot>

    @include('finance._nav')

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('finance.payments') }}" class="flex flex-wrap gap-4 items-end">
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="FAILED" {{ request('status') === 'FAILED' ? 'selected' : '' }}>Failed</option>
                    <option value="REFUNDED" {{ request('status') === 'REFUNDED' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            @if(request()->has('status'))
                <a href="{{ route('finance.payments') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Receipt #</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Invoice / Guest</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($payments as $pay)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $pay->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-brand-600 dark:text-brand-400">
                                {{ $pay->receipt_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('finance.invoices.show', $pay->invoice) }}" class="text-sm font-medium text-slate-900 dark:text-white hover:underline block">
                                    {{ $pay->invoice->invoice_number ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $pay->invoice->guest->first_name ?? '' }} {{ $pay->invoice->guest->last_name ?? '' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ str_replace('_', ' ', $pay->payment_method) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white">
                                KES {{ number_format($pay->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $sColors = ['COMPLETED' => 'green', 'PENDING' => 'yellow', 'FAILED' => 'red', 'REFUNDED' => 'slate'];
                                @endphp
                                <x-status-badge :color="$sColors[$pay->status] ?? 'gray'" :text="$pay->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
