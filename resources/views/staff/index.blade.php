<x-app-layout title="Staff Directory">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Staff & Roles</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage employee records and system access.</p>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                @can('users.create')
                    <a href="{{ route('staff.create') }}" class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Employee
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    @include('staff._nav')

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-4 sm:p-5 mb-6 flex flex-wrap gap-4 items-end">
        <form method="GET" action="{{ route('staff.index') }}" class="flex-1 w-full min-w-0 sm:min-w-[300px]">
            <x-input-label for="search" value="Search Staff" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all placeholder:text-slate-400" placeholder="Search by name, ID or email...">
            </div>
        </form>
        
        @if(request()->has('search'))
            <a href="{{ route('staff.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-xl transition-colors">Clear</a>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700/50">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Department & Role</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">System Access</th>
                        <th class="px-6 py-3.5 text-right"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($emp->first_name, 0, 1) }}{{ substr($emp->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $emp->full_name }}</div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $emp->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900 dark:text-white font-medium">{{ $emp->position }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $emp->department->name ?? 'No Department' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-600 dark:text-slate-400">{{ $emp->email ?? 'No email' }}</div>
                                <div class="text-sm text-slate-500 mt-0.5">{{ $emp->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($emp->user)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $emp->user->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300' }}">
                                        {{ $emp->user->roles->pluck('name')->join(', ') ?: 'No Role' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-700/50 dark:text-slate-400">
                                        No Access
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end gap-3">
                                @if($emp->user && $emp->email)
                                    <form action="{{ route('admin.unlock-user') }}" method="POST" class="inline-block m-0 p-0">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $emp->email }}">
                                        <button type="submit" onclick="return confirm('Are you sure you want to unlock this user\'s login rate limit?')" class="inline-flex min-h-11 items-center text-amber-600 hover:text-amber-900 dark:text-amber-500 dark:hover:text-amber-400 transition-colors">
                                            Unlock
                                        </button>
                                    </form>
                                @endif
                                @can('users.update')
                                    <a href="{{ route('staff.edit', $emp) }}" class="inline-flex min-h-11 items-center text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 transition-colors">
                                        Edit
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No employees found</h3>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
