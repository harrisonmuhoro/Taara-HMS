<div class="flex items-center gap-1 overflow-x-auto bg-slate-100/50 dark:bg-slate-800/30 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/50 mb-6">
    <a href="{{ route('staff.index') }}" 
       class="px-3 sm:px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('staff.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Staff Directory
    </a>
    @can('roles.manage')
    <a href="{{ route('roles.index') }}" 
       class="px-3 sm:px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ request()->routeIs('roles.*') ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white/60 dark:hover:bg-slate-800/60' }}">
        Roles & Permissions
    </a>
    @endcan
</div>
