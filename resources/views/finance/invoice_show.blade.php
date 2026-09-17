<x-app-layout :title="'Invoice ' . $invoice->invoice_number">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Finance', 'url' => '#'],
            ['label' => 'Invoices', 'url' => route('finance.invoices')],
            ['label' => $invoice->invoice_number, 'url' => '#'],
        ]" />
        
        <div class="mt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $invoice->invoice_number }}</h1>
                @php
                    $sColors = ['PAID' => 'green', 'PARTIALLY_PAID' => 'yellow', 'UNPAID' => 'red', 'VOID' => 'gray'];
                @endphp
                <x-status-badge :color="$sColors[$invoice->status] ?? 'gray'" :text="str_replace('_', ' ', $invoice->status)" />
            </div>
            
            <div class="flex gap-2">
                <button type="button" class="btn-secondary" onclick="window.print()">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        
        {{-- Invoice Header / Print Area --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-8 shadow-sm print:shadow-none print:border-none print:text-black">
            
            <div class="flex justify-between items-start mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-brand-600 dark:text-brand-400 print:text-black">INVOICE</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-mono">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-slate-900 dark:text-white print:text-black">Enterprise Hotel Management</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $invoice->branch->name ?? 'Main Branch' }}</p>
                </div>
            </div>

            <div class="flex justify-between mb-12 pb-8 border-b border-slate-100 dark:border-slate-700/50 print:border-slate-300">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Billed To</p>
                    <p class="font-semibold text-slate-900 dark:text-white print:text-black">{{ $invoice->guest->first_name ?? 'N/A' }} {{ $invoice->guest->last_name ?? '' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $invoice->guest->email ?? '' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $invoice->guest->phone ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date Issued</p>
                        <p class="font-medium text-slate-900 dark:text-white print:text-black">{{ $invoice->issued_at ? $invoice->issued_at->format('M j, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date Due</p>
                        <p class="font-medium text-slate-900 dark:text-white print:text-black">{{ $invoice->due_at ? $invoice->due_at->format('M j, Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <table class="w-full mb-8 text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-200 dark:border-slate-700 print:border-slate-400">
                        <th class="py-3 text-sm font-semibold text-slate-900 dark:text-white print:text-black">Description</th>
                        <th class="py-3 text-right text-sm font-semibold text-slate-900 dark:text-white print:text-black">Qty</th>
                        <th class="py-3 text-right text-sm font-semibold text-slate-900 dark:text-white print:text-black">Unit Price</th>
                        <th class="py-3 text-right text-sm font-semibold text-slate-900 dark:text-white print:text-black">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 print:divide-slate-200">
                    @forelse ($invoice->items as $item)
                        <tr>
                            <td class="py-4 text-sm text-slate-700 dark:text-slate-300 print:text-black">{{ $item->description }}</td>
                            <td class="py-4 text-right text-sm text-slate-700 dark:text-slate-300 print:text-black">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-sm text-slate-700 dark:text-slate-300 print:text-black">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-4 text-right text-sm font-medium text-slate-900 dark:text-white print:text-black">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-sm text-slate-500">No items found for this invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Totals --}}
            <div class="flex justify-end">
                <dl class="w-full sm:w-1/2 lg:w-1/3 space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600 dark:text-slate-400 print:text-slate-800">
                        <dt>Subtotal</dt>
                        <dd>KES {{ number_format($invoice->subtotal, 2) }}</dd>
                    </div>
                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <dt>Discount</dt>
                            <dd>- KES {{ number_format($invoice->discount_amount, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-600 dark:text-slate-400 print:text-slate-800">
                        <dt>Tax (16%)</dt>
                        <dd>KES {{ number_format($invoice->tax_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-3 text-base font-bold text-slate-900 dark:text-white print:text-black">
                        <dt>Grand Total</dt>
                        <dd>KES {{ number_format($invoice->grand_total, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400 pt-1">
                        <dt>Amount Paid</dt>
                        <dd>- KES {{ number_format($invoice->amount_paid, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-3 text-base font-bold {{ $invoice->balance_due > 0 ? 'text-red-500' : 'text-slate-900 dark:text-white print:text-black' }}">
                        <dt>Balance Due</dt>
                        <dd>KES {{ number_format($invoice->balance_due, 2) }}</dd>
                    </div>
                </dl>
            </div>
            
        </div>

        {{-- Payment History --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 no-print">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Payment History</h2>
            @if ($invoice->payments->isEmpty())
                <p class="text-sm text-slate-500 dark:text-slate-400">No payments have been recorded for this invoice.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700/50">
                        <thead>
                            <tr>
                                <th class="pb-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Receipt</th>
                                <th class="pb-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Date</th>
                                <th class="pb-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Method</th>
                                <th class="pb-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Amount</th>
                                <th class="pb-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                            @foreach ($invoice->payments as $payment)
                                <tr>
                                    <td class="py-3 text-sm font-mono text-brand-600 dark:text-brand-400">{{ $payment->receipt_number ?? 'N/A' }}</td>
                                    <td class="py-3 text-sm text-slate-600 dark:text-slate-300">{{ $payment->created_at->format('M j, Y H:i') }}</td>
                                    <td class="py-3 text-sm text-slate-600 dark:text-slate-300">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td class="py-3 text-sm font-medium text-slate-900 dark:text-white text-right">KES {{ number_format($payment->amount, 2) }}</td>
                                    <td class="py-3 text-right">
                                        <x-status-badge :color="$payment->status === 'COMPLETED' ? 'green' : ($payment->status === 'FAILED' ? 'red' : 'yellow')" :text="$payment->status" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
    </div>
</x-app-layout>
