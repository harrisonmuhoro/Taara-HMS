<div class="flex items-center gap-2 mb-6 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
    @can('invoices.view')
        <a href="{{ route('finance.invoices') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('finance.invoices*') ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            Invoices
        </a>
    @endcan
    @can('payments.view')
        <a href="{{ route('finance.payments') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('finance.payments*') ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            Payments
        </a>
    @endcan
    @can('expenses.view')
        <a href="{{ route('finance.expenses.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('finance.expenses*') ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            Expenses
        </a>
    @endcan
    @can('payments.refund')
        <a href="{{ route('finance.refunds.index') }}" 
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('finance.refunds*') ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
            Refunds
        </a>
    @endcan
</div>
