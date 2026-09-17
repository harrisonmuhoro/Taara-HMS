<x-app-layout title="Audit Logs">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Audit Logs', 'url' => route('audit-logs.index')]]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Audit logs</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review security-sensitive changes and operational activity.</p>
    </x-slot>

    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700/50 dark:bg-slate-800/60">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div><x-input-label for="search" value="Search" /><x-text-input id="search" name="search" value="{{ request('search') }}" placeholder="Action, entity, user, IP" class="mt-1 block w-full" /></div>
            <div><x-input-label for="action" value="Action" /><select id="action" name="action" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 text-sm dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100"><option value="">All actions</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>@endforeach</select></div>
            <div><x-input-label for="from" value="From" /><x-text-input id="from" name="from" type="date" value="{{ request('from') }}" class="mt-1 block w-full" /></div>
            <div><x-input-label for="to" value="To" /><x-text-input id="to" name="to" type="date" value="{{ request('to') }}" class="mt-1 block w-full" /></div>
        </div>
        <div class="mt-4 flex flex-wrap gap-3"><x-primary-button>Apply filters</x-primary-button><a href="{{ route('audit-logs.export', request()->query()) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-700">Export CSV</a><a href="{{ route('audit-logs.index') }}" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Clear</a></div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700/50 dark:bg-slate-800/60">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-900/50 dark:text-slate-400"><tr><th class="px-5 py-3">Time</th><th class="px-5 py-3">Action</th><th class="px-5 py-3">Entity</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Branch</th><th class="px-5 py-3">IP</th></tr></thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800"><td class="whitespace-nowrap px-5 py-4 text-slate-500 dark:text-slate-400">{{ $log->created_at?->format('d M Y, H:i') }}</td><td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $log->action }}</td><td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ class_basename($log->entity_type) }}{{ $log->entity_id ? ' #'.$log->entity_id : '' }}</td><td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->user?->name ?? 'System' }}</td><td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->branch?->name ?? 'All branches' }}</td><td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $log->ip_address ?? '—' }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No audit events match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">{{ $logs->links() }}</div>@endif
    </div>
</x-app-layout>
