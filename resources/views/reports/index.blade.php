<x-app-layout title="Reports Dashboard">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Reports Overview</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">High-level metrics and performance indicators.</p>
    </x-slot>

    @include('reports._nav')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Revenue Card --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-50 dark:bg-brand-900/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-brand-100 dark:bg-brand-900/40 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Revenue</h3>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">${{ number_format($metrics['total_revenue'], 2) }}</div>
                </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="{{ route('reports.revenue') }}" class="text-sm font-medium text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300">View Details &rarr;</a>
            </div>
        </div>

        {{-- Occupancy Card --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Occupancy Rate</h3>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($metrics['occupancy_rate'], 1) }}%</div>
                </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $metrics['occupied_rooms'] }} of {{ $metrics['total_rooms'] }} rooms occupied</p>
            </div>
        </div>
        
        {{-- Inventory Alert Card --}}
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 dark:bg-amber-900/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">Inventory Status</h3>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white mt-1">Check Levels</div>
                </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="{{ route('reports.inventory') }}" class="text-sm font-medium text-amber-600 dark:text-amber-500 hover:text-amber-800 dark:hover:text-amber-400">View Inventory Report &rarr;</a>
            </div>
        </div>
    </div>
</x-app-layout>
