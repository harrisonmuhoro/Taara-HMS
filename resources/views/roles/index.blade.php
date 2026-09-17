<x-app-layout title="System Roles">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Roles & Permissions</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage security roles and system access.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" x-data="" @click="$dispatch('open-modal', 'create-role')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Create Role
                </button>
            </div>
        </div>
    </x-slot>

    @include('staff._nav')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 flex flex-col hover:border-brand-300 dark:hover:border-brand-700 transition-colors shadow-sm relative">
                @if($role->name === 'Super Administrator')
                    <div class="absolute top-0 right-0 mt-4 mr-4 px-2 py-1 bg-amber-100 text-amber-800 text-[10px] uppercase font-bold tracking-wider rounded-lg">Built-in</div>
                @endif
                
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $role->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 mb-6 flex-1">{{ $role->description ?? 'No description provided.' }}</p>
                
                <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-auto">
                    <div class="flex -space-x-2">
                        @if($role->users_count > 0)
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-medium text-slate-600 dark:text-slate-300 ring-2 ring-white dark:ring-slate-800">
                                {{ $role->users_count }}
                            </div>
                            <span class="pl-4 text-xs font-medium text-slate-500">Users</span>
                        @else
                            <span class="text-xs font-medium text-slate-400">No users assigned</span>
                        @endif
                    </div>
                    
                    @if($role->name !== 'Super Administrator')
                        <a href="{{ route('roles.permissions', $role) }}" class="text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 text-sm font-medium transition-colors">
                            Manage Permissions &rarr;
                        </a>
                    @else
                        <span class="text-slate-400 text-sm italic">Full Access</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Create Role Modal --}}
    <x-modal name="create-role" focusable>
        <form method="post" action="{{ route('roles.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Create New Role</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Add a new security role. You can assign permissions after creation.</p>

            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="name" value="Role Name *" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" placeholder="e.g. Night Auditor" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-sm transition-colors">
                    Create Role
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
