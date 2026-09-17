<x-app-layout title="Manage Permissions: {{ $role->name }}">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Staff & Roles', 'url' => route('staff.index')], ['label' => 'Roles', 'url' => route('roles.index')], ['label' => $role->name, 'url' => '']]" />
        
        <div class="mt-3">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manage Permissions</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Role: <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $role->name }}</span></p>
        </div>
    </x-slot>

    @include('staff._nav')

    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @foreach($groupedPermissions as $group => $permissions)
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm flex flex-col">
                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700/50">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ ucfirst($group) }}</h3>
                    </div>
                    <div class="p-5 flex-1 space-y-3">
                        @foreach($permissions as $permission)
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                           class="w-4 h-4 text-brand-600 border-slate-300 rounded focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-slate-900 dark:text-slate-200 group-hover:text-brand-600 transition-colors">{{ $permission->description }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $permission->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-3 sm:gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
            <a href="{{ route('roles.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                Save Permissions
            </button>
        </div>
    </form>
</x-app-layout>
