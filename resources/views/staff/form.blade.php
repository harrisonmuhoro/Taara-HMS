<x-app-layout title="{{ isset($employee) ? 'Edit Employee' : 'Add Employee' }}">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Staff & Roles', 'url' => route('staff.index')], ['label' => isset($employee) ? 'Edit Employee' : 'Add Employee', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">{{ isset($employee) ? 'Edit Employee: ' . $employee->full_name : 'Add New Employee' }}</h1>
    </x-slot>

    @include('staff._nav')

    <div class="max-w-4xl" x-data="{ createAccount: {{ old('create_account', isset($employee) && $employee->user ? 'true' : 'false') }} }">
        <form method="POST" action="{{ isset($employee) ? route('staff.update', $employee) : route('staff.store') }}">
            @csrf
            @if(isset($employee))
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- HR Profile --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-5 border-b border-slate-200 dark:border-slate-700/50 pb-2">Employee Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="first_name" value="First Name *" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $employee->first_name ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <div>
                            <x-input-label for="last_name" value="Last Name *" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $employee->last_name ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email Address" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $employee->email ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Phone Number" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $employee->phone ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="department_id" value="Department *" />
                            <select id="department_id" name="department_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
                        </div>

                        <div>
                            <x-input-label for="position" value="Job Title / Position *" />
                            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $employee->position ?? '')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('position')" />
                        </div>

                        @if(!isset($employee))
                            <div>
                                <x-input-label for="hire_date" value="Hire Date *" />
                                <x-text-input id="hire_date" name="hire_date" type="date" class="mt-1 block w-full" :value="old('hire_date', date('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('hire_date')" />
                            </div>
                        @else
                            <div>
                                <x-input-label for="employment_status" value="Employment Status *" />
                                <select id="employment_status" name="employment_status" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 rounded-md shadow-sm" required>
                                    <option value="active" {{ old('employment_status', $employee->employment_status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('employment_status', $employee->employment_status) === 'inactive' ? 'selected' : '' }}>Inactive / Leave</option>
                                    <option value="terminated" {{ old('employment_status', $employee->employment_status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- User Account --}}
                <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5 border-b border-slate-200 dark:border-slate-700/50 pb-2">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">System Access (Optional)</h2>
                        
                        @if(!isset($employee) || !$employee->user)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="create_account" value="1" class="sr-only peer" x-model="createAccount">
                                <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-brand-600"></div>
                                <span class="ms-3 text-sm font-medium text-slate-900 dark:text-slate-300">Grant Login Access</span>
                            </label>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                Active Account
                            </span>
                        @endif
                    </div>

                    <div x-show="createAccount" x-transition.opacity class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="role_id" value="System Role *" />
                            <select id="role_id" name="role_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 rounded-md shadow-sm" x-bind:required="createAccount">
                                <option value="">Select Role</option>
                                @php
                                    $currentRole = isset($employee) && $employee->user ? $employee->user->roles->first()?->id : null;
                                @endphp
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $currentRole) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                        </div>

                        <div>
                            <x-input-label for="password" value="{{ isset($employee) && $employee->user ? 'Reset Password (Optional)' : 'Initial Password *' }}" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" x-bind:required="createAccount && !{{ isset($employee) && $employee->user ? 'true' : 'false' }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 sm:gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                <a href="{{ route('staff.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                    {{ isset($employee) ? 'Update Employee' : 'Create Employee' }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
