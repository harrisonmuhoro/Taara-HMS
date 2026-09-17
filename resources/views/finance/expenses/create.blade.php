<x-app-layout title="Log Expense">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Finance', 'url' => '#'], ['label' => 'Expenses', 'url' => route('finance.expenses.index')], ['label' => 'Log Expense', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Log New Expense</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Record a new operational expense for approval.</p>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
            <form method="POST" action="{{ route('finance.expenses.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="expense_date" value="Date *" />
                            <x-text-input id="expense_date" name="expense_date" type="
                            date" class="mt-1 block w-full" :value="old('expense_date', date('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('expense_date')" />
                        </div>
                        <div>
                            <x-input-label for="attachment" value="Receipt attachment" />
                            <input id="attachment" name="attachment" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-md border border-gray-300 p-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                            <p class="mt-1 text-xs text-slate-500">PDF or image, maximum 5 MB.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                        </div>
                        
                        <div>
                            <x-input-label for="category_id" value="Category *" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>
                    </div>

                    {{-- Description & Amount --}}
                    <div>
                        <x-input-label for="description" value="Description *" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="amount" value="Amount (KES) *" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full font-semibold" :value="old('amount')" placeholder="0.00" required />
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <div>
                            <x-input-label for="receipt_number" value="Receipt Number" />
                            <x-text-input id="receipt_number" name="receipt_number" type="text" class="mt-1 block w-full font-mono text-sm" :value="old('receipt_number')" placeholder="Optional reference" />
                            <x-input-error class="mt-2" :messages="$errors->get('receipt_number')" />
                        </div>
                    </div>

                    {{-- Payment Details --}}
                    <div>
                        <x-input-label for="payment_method_id" value="Payment Method (If Paid)" />
                        <select id="payment_method_id" name="payment_method_id" class="mt-1 block w-full md:w-1/2 border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm">
                            <option value="">None (Pending Payment)</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Leave empty if this expense is to be paid later.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('payment_method_id')" />
                    </div>

                </div>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                    <a href="{{ route('finance.expenses.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        Log Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
