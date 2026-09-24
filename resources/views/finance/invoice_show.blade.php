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


        {{-- M-Pesa Payment Section --}}
        @if($invoice->balance_due > 0)
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Pay via M-Pesa</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Send an STK Push prompt to the guest's phone to collect KES {{ number_format($invoice->balance_due, 2) }}.</p>
                </div>
                <button type="button" onclick="document.getElementById('mpesa-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500 active:scale-95 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Pay with M-Pesa
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- M-Pesa Modal --}}
    <div id="mpesa-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">M-Pesa Payment</h3>
                            <p class="text-green-100 text-xs">Lipa Na M-Pesa STK Push</p>
                        </div>
                    </div>
                    <button onclick="closeMpesaModal()" class="text-white/70 hover:text-white transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div id="mpesa-form-area">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 mb-5 flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-300">Amount Due</span>
                        <span class="text-xl font-bold text-green-700 dark:text-green-400">KES {{ number_format($invoice->balance_due, 2) }}</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="mpesa-phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Guest Phone Number</label>
                            <input type="tel" id="mpesa-phone" placeholder="0712345678" value="{{ $invoice->guest->phone ?? '' }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500"/>
                            <p class="mt-1 text-xs text-slate-400">e.g. 0712345678 or 254712345678</p>
                        </div>
                        <div>
                            <label for="mpesa-amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Amount (KES)</label>
                            <input type="number" id="mpesa-amount" value="{{ intval($invoice->balance_due) }}" min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500"/>
                        </div>
                    </div>
                </div>
                <div id="mpesa-status" class="hidden text-center py-6">
                    <div id="mpesa-spinner" class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                        <svg class="w-7 h-7 text-green-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    </div>
                    <p class="font-semibold text-slate-800 dark:text-white" id="mpesa-status-title">Sending prompt...</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" id="mpesa-status-msg">Check the guest's phone for the M-Pesa PIN prompt.</p>
                </div>
                <div id="mpesa-actions" class="flex gap-3 pt-2">
                    <button onclick="closeMpesaModal()" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Cancel</button>
                    <button onclick="sendStkPush()" id="mpesa-submit-btn" class="flex-1 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-500 active:scale-95 transition-all">Send STK Push</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function closeMpesaModal() {
            document.getElementById('mpesa-modal').classList.add('hidden');
            document.getElementById('mpesa-form-area').classList.remove('hidden');
            document.getElementById('mpesa-status').classList.add('hidden');
            document.getElementById('mpesa-actions').classList.remove('hidden');
            document.getElementById('mpesa-submit-btn').classList.remove('hidden');
        }
        async function sendStkPush() {
            const phone = document.getElementById('mpesa-phone').value.trim();
            const amount = document.getElementById('mpesa-amount').value.trim();
            if (!phone || !amount) { alert('Please enter a phone number and amount.'); return; }
            document.getElementById('mpesa-form-area').classList.add('hidden');
            document.getElementById('mpesa-status').classList.remove('hidden');
            document.getElementById('mpesa-actions').classList.add('hidden');
            try {
                const response = await fetch('/api/mpesa/stkpush/initiate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ phone, amount })
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('mpesa-spinner').innerHTML = '<svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
                    document.getElementById('mpesa-spinner').className = 'inline-flex items-center justify-center w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 mb-4';
                } else {
                    document.getElementById('mpesa-spinner').innerHTML = '<svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                    document.getElementById('mpesa-spinner').className = 'inline-flex items-center justify-center w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 mb-4';
                }
                document.getElementById('mpesa-status-title').textContent = data.success ? 'Prompt Sent!' : 'Payment Failed';
                document.getElementById('mpesa-status-msg').textContent = data.message;
                document.getElementById('mpesa-actions').classList.remove('hidden');
                document.getElementById('mpesa-submit-btn').classList.add('hidden');
            } catch (err) {
                document.getElementById('mpesa-status-title').textContent = 'Error';
                document.getElementById('mpesa-status-msg').textContent = 'Something went wrong. Please try again.';
                document.getElementById('mpesa-actions').classList.remove('hidden');
            }
        }
    </script>
    @endpush

</x-app-layout>
