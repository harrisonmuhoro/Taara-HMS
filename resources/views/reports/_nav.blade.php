<div class="flex flex-wrap items-center justify-between gap-4 bg-slate-100/50 dark:bg-slate-800/30 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/50 mb-6">
    <div class="flex flex-wrap items-center gap-1">
        <a href="{{ route('reports.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
            Dashboard
        </a>
        <a href="{{ route('reports.revenue') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.revenue') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
            Revenue
        </a>
        <a href="{{ route('reports.inventory') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('reports.inventory') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
            Inventory
        </a>
    </div>

    @if(isset($startDate) && isset($endDate))
    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 px-2">
        <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm h-8" required>
        <span class="text-slate-400 text-xs">to</span>
        <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 rounded-md shadow-sm h-8" required>
        <button type="submit" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-medium rounded-md transition-colors">Apply</button>
    </form>
    @endif
</div>
