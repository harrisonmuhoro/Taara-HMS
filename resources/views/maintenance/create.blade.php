<x-app-layout title="Report Issue">
    <x-slot name="header">
        <x-breadcrumb :links="[['label' => 'Maintenance', 'url' => route('maintenance.index')], ['label' => 'Report Issue', 'url' => '']]" />
        <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">Report Maintenance Issue</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a new ticket for an issue that requires attention.</p>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6 shadow-sm">
            <form method="POST" action="{{ route('maintenance.store') }}">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="room_id" value="Room *" />
                            <select id="room_id" name="room_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} ({{ $room->operational_status }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" value="Category *" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="title" value="Issue Title *" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" placeholder="e.g., Leaking faucet in bathroom" required />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Detailed Description *" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm" required placeholder="Describe the issue in detail...">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-input-label for="priority" value="Priority Level *" />
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'] as $priority)
                                <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-slate-800 p-4 shadow-sm focus:outline-none hover:bg-slate-50 dark:hover:bg-slate-700/50 has-[:checked]:ring-2 has-[:checked]:ring-brand-500 has-[:checked]:border-brand-500 dark:has-[:checked]:border-brand-500 dark:border-slate-700">
                                    <input type="radio" name="priority" value="{{ $priority }}" class="sr-only" {{ old('priority', 'MEDIUM') === $priority ? 'checked' : '' }}>
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900 dark:text-white capitalize">{{ strtolower($priority) }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-slate-200 dark:border-slate-700/50 pt-5">
                    <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-brand-500/20 transition-all duration-200 hover:shadow-brand-500/30">
                        Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
