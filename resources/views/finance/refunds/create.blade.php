<x-app-layout title="Process Refund">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Finance', 'url' => '#'], ['label' => 'Refunds', 'url' => route('finance.refunds.index')], ['label' => 'Process Refund', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Process Refund</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Issue a refund against an existing payment.</p>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
            @if(!$payment)
                {{-- Step 1: Select Payment --}}
                <form method="GET" action="{{ route('finance.refunds.create') }}" class="mb-6">
                    <x-input-label for="payment_id" value="Enter Payment ID to Refund" />
                    <div class="flex gap-4 mt-1">
                        <x-text-input id="payment_id" name="payment_id" type="number" class="block w-full" :value="request('payment_id')" placeholder="e.g. 12" required />
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm font-medium rounded-xl transition-colors whitespace-nowrap">
                            Load Details
                        </button>
                    </div>
                </form>
            @else
                {{-- Step 2: Process Refund Form --}}
                <div class="mb-8 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Payment Details</h3>
                        <a href="{{ route('finance.refunds.create') }}" class="text-xs text-brand-600 hover:underline">Change Payment</a>
                    </div>
                    <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                        <li><strong>Receipt:</strong> {{ $payment->receipt_number ?? 'N/A' }}</li>
                        <li><strong>Original Amount:</strong> KES {{ number_format($payment->amount, 2) }}</li>
                        <li><strong>Method:</strong> {{ str_replace('_', ' ', $payment->payment_method) }}</li>
                        <li><strong>Date:</strong> {{ $payment->created_at->format('M j, Y') }}</li>
                        <li><strong>Invoice:</strong> {{ $payment->invoice->invoice_number }} ({{ $payment->invoice->guest->first_name ?? '' }} {{ $payment->invoice->guest->last_name ?? '' }})</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('finance.refunds.store') }}">
                    @csrf
                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="amount" value="Refund Amount (KES) *" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $payment->amount }}" class="mt-1 block w-full font-semibold" :value="old('amount', $payment->amount)" required />
                            <p class="text-xs text-slate-500 mt-1">Maximum allowed: KES {{ number_format($payment->amount, 2) }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <div>
                            <x-input-label for="refund_method" value="Refund Method *" />
                            <select id="refund_method" name="refund_method" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="CASH">Cash</option>
                                <option value="MPESA">M-Pesa</option>
                                <option value="CARD">Credit/Debit Card</option>
                                <option value="BANK_TRANSFER">Bank Transfer</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('refund_method')" />
                        </div>

                        <div>
                            <x-input-label for="reason" value="Reason for Refund *" />
                            <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required placeholder="e.g. Guest cancellation, overpayment...">{{ old('reason') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                        <a href="{{ route('finance.refunds.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                            Cancel
                        </a>
                        <button type="submit" onclick="return confirm('Are you sure you want to process this refund? This cannot be undone.')" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-rose-500/20 transition-all duration-200 hover:shadow-rose-500/30">
                            Process Refund
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
