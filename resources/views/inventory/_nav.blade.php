<div class="flex flex-wrap items-center gap-1 bg-slate-100/50 dark:bg-slate-800/30 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/50 mb-6">
    <a href="{{ route('inventory.products.index') }}" 
       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.products.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Products
    </a>
    <a href="{{ route('inventory.suppliers.index') }}" 
       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.suppliers.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Suppliers
    </a>
    <a href="{{ route('inventory.purchases.index') }}" 
       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.purchases.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Purchase Orders
    </a>
    <a href="{{ route('inventory.adjustments.index') }}" 
       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.adjustments.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Stock Adjustments
    </a>
</div>
