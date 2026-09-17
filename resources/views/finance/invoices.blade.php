<x-app-layout title="Invoices">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-breadcrumb :links="[['label' => 'Finance', 'url' => '#'], ['label' => 'Invoices', 'url' => route('finance.invoices')]]" />
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Invoices</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Manage guest invoices and track payments.
                </p>
            </div>
            
            {{-- Stats --}}
            <div class="flex items-center gap-4 bg-white dark:bg-slate-800/60 p-2 rounded-xl border border-slate-200 dark:border-slate-700/50">
                <div class="px-3 text-center">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Unpaid</p>
                    <p class="text-lg font-bold text-red-500 dark:text-red-400">KES {{ number_format($stats['total_unpaid'], 2) }}</p>
                </div>
                <div class="w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                <div class="px-3 text-center">
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Paid</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">KES {{ number_format($stats['total_paid'], 2) }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    @include('finance._nav')

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-5 mb-6">
        <form method="GET" action="{{ route('finance.invoices') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, Guest Name…" class="block w-full pl-9 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-3 pr-8 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all appearance-none">
                    <option value="">All Statuses</option>
                    <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Paid</option>
                    <option value="PARTIALLY_PAID" {{ request('status') === 'PARTIALLY_PAID' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="UNPAID" {{ request('status') === 'UNPAID' ? 'selected' : '' }}>Unpaid</option>
                    <option value="VOID" {{ request('status') === 'VOID' ? 'selected' : '' }}>Void</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors">Search</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('finance.invoices') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($invoices as $inv)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-brand-600 dark:text-brand-400">
                                {{ $inv->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $inv->guest->first_name ?? 'N/A' }} {{ $inv->guest->last_name ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                {{ $inv->issued_at ? $inv->issued_at->format('M j, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white">
                                KES {{ number_format($inv->grand_total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $inv->balance_due > 0 ? 'text-red-500' : 'text-slate-500' }}">
                                KES {{ number_format($inv->balance_due, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $sColors = ['PAID' => 'green', 'PARTIALLY_PAID' => 'yellow', 'UNPAID' => 'red', 'VOID' => 'gray'];
                                @endphp
                                <x-status-badge :color="$sColors[$inv->status] ?? 'gray'" :text="str_replace('_', ' ', $inv->status)" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('finance.invoices.show', $inv) }}" class="p-1.5 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg transition-colors inline-block" title="View">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
