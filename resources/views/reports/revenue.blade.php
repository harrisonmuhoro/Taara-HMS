<x-app-layout title="Revenue Report">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Revenue Report</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Detailed breakdown of income.</p>
    </x-slot>

    @include('reports._nav')
    <div class="mb-6 flex justify-end gap-3 print:hidden"><button type="button" onclick="window.print()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Print</button><a href="{{ route('reports.export.queue', ['type' => 'revenue'] + request()->query()) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Queue CSV</a><a href="{{ route('reports.revenue.export', request()->query()) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Export CSV</a><a href="{{ route('reports.revenue.export.pdf', request()->query()) }}" class="rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700">Export PDF</a></div>

    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm mb-8">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Daily Revenue Trend</h2>
        </div>
        <div class="p-6">
            {{-- Simple Bar Chart Representation --}}
            <div class="h-64 flex items-end gap-2">
                @php
                    $maxVal = $revenueData->max('total') ?: 1;
                @endphp
                @foreach($revenueData as $data)
                    @php
                        $height = ($data->total / $maxVal) * 100;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 group relative">
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full mb-2 hidden group-hover:block z-10 w-max bg-slate-900 text-white text-xs py-1 px-2 rounded shadow-lg">
                            {{ Carbon\Carbon::parse($data->date)->format('M d') }}: ${{ number_format($data->total, 2) }}
                        </div>
                        <div class="w-full bg-brand-500 dark:bg-brand-600 rounded-t-sm transition-all duration-300 group-hover:bg-brand-400" style="height: {{ $height }}%"></div>
                        <div class="text-[10px] text-slate-500 truncate w-full text-center hidden md:block">{{ Carbon\Carbon::parse($data->date)->format('d/m') }}</div>
                    </div>
                @endforeach
                @if($revenueData->isEmpty())
                    <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">No revenue data for this period.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Paid Invoices</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                {{ $invoice->issued_at ? $invoice->issued_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                {{ $invoice->guest?->full_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-brand-600 dark:text-brand-400">
                                ${{ number_format($invoice->grand_total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">No invoices found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
