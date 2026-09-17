<x-app-layout title="Hotel Configuration">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</a>
                    <span>/</span>
                    <span>Hotel Configuration</span>
                </div>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Hotel configuration</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage branches, room setup, amenities, and booking channels.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300">
                <p class="font-medium">Please correct the following:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @can('settings.manage')
            <div class="grid gap-6 xl:grid-cols-2">
                <x-card title="Add branch" subtitle="Create another property location.">
                    <form method="POST" action="{{ route('configuration.branches.store') }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        <x-input name="name" label="Name" required />
                        <x-input name="code" label="Code" required />
                        <x-input name="address" label="Address" />
                        <x-input name="phone" label="Phone" />
                        <x-input name="email" type="email" label="Email" />
                        <div class="sm:col-span-2"><x-primary-button>Add branch</x-primary-button></div>
                    </form>
                </x-card>

                <x-card title="Add department" subtitle="Organize operational teams by branch.">
                    <form method="POST" action="{{ route('configuration.departments.store') }}" class="space-y-4">
                        @csrf
                        <x-select name="branch_id" label="Branch" required>
                            @foreach ($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
                        </x-select>
                        <x-input name="name" label="Name" required />
                        <x-textarea name="description" label="Description" />
                        <x-primary-button>Add department</x-primary-button>
                    </form>
                </x-card>

                <x-card title="Add floor" subtitle="Define the floors used by room inventory.">
                    <form method="POST" action="{{ route('configuration.floors.store') }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        <x-select name="branch_id" label="Branch" required>
                            @foreach ($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
                        </x-select>
                        <x-input name="floor_number" type="number" label="Floor number" required />
                        <x-input name="name" label="Display name" />
                        <x-input name="description" label="Description" />
                        <div class="sm:col-span-2"><x-primary-button>Add floor</x-primary-button></div>
                    </form>
                </x-card>

                <x-card title="Add room type" subtitle="Set the defaults used when creating rooms.">
                    <form method="POST" action="{{ route('configuration.room-types.store') }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        <x-select name="branch_id" label="Branch" required>
                            @foreach ($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
                        </x-select>
                        <x-input name="name" label="Name" required />
                        <x-input name="base_rate" type="number" step="0.01" label="Base rate" required />
                        <x-input name="max_adults" type="number" min="1" label="Max adults" required />
                        <x-input name="max_children" type="number" min="0" label="Max children" required />
                        <x-input name="bed_type" label="Bed type" />
                        <x-input name="bed_count" type="number" min="1" label="Bed count" required />
                        <x-textarea name="description" label="Description" />
                        <div class="sm:col-span-2"><x-primary-button>Add room type</x-primary-button></div>
                    </form>
                </x-card>

                <x-card title="Add amenity" subtitle="Maintain the amenities available to guests.">
                    <form method="POST" action="{{ route('configuration.amenities.store') }}" class="space-y-4">
                        @csrf
                        <x-input name="name" label="Name" required />
                        <x-textarea name="description" label="Description" />
                        <x-primary-button>Add amenity</x-primary-button>
                    </form>
                </x-card>

                <x-card title="Add booking source" subtitle="Track direct, OTA, agent, and corporate channels.">
                    <form method="POST" action="{{ route('configuration.booking-sources.store') }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        <x-select name="branch_id" label="Branch" required>
                            @foreach ($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach
                        </x-select>
                        <x-input name="name" label="Name" required />
                        <x-select name="type" label="Type" required>
                            <option value="DIRECT">Direct</option><option value="OTA">OTA</option><option value="TRAVEL_AGENT">Travel agent</option><option value="CORPORATE">Corporate</option>
                        </x-select>
                        <x-input name="commission_rate" type="number" min="0" max="100" step="0.01" label="Commission %" />
                        <div class="sm:col-span-2"><x-primary-button>Add source</x-primary-button></div>
                    </form>
                </x-card>
            </div>
        @endcan

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ($branches as $branch)
                <x-card title="{{ $branch->name }}" subtitle="{{ $branch->code }} · {{ ucfirst($branch->status) }}">
                    <div class="grid grid-cols-3 gap-3 text-center text-sm">
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-800/70"><div class="text-lg font-semibold text-slate-900 dark:text-white">{{ $branch->departments->count() }}</div><div class="text-slate-500">Departments</div></div>
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-800/70"><div class="text-lg font-semibold text-slate-900 dark:text-white">{{ $branch->floors->count() }}</div><div class="text-slate-500">Floors</div></div>
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-800/70"><div class="text-lg font-semibold text-slate-900 dark:text-white">{{ $branch->roomTypes->count() }}</div><div class="text-slate-500">Room types</div></div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <x-card title="Amenities" subtitle="Global amenities available across the system.">
            <div class="flex flex-wrap gap-2">
                @forelse ($amenities as $amenity)<span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $amenity->name }}</span>@empty<span class="text-sm text-slate-500">No amenities configured yet.</span>@endforelse
            </div>
        </x-card>
    </div>
</x-app-layout>
