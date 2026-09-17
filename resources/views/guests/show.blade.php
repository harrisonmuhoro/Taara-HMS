<x-app-layout :title="$guest->first_name . ' ' . $guest->last_name">
    <x-slot name="header">
        <x-breadcrumb :links="[
            ['label' => 'Guests', 'url' => route('guests.index')],
            ['label' => $guest->first_name . ' ' . $guest->last_name, 'url' => '#'],
        ]" />
        <div class="mt-3 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-bold text-2xl shadow-sm shadow-brand-500/30">
                    {{ strtoupper(substr($guest->first_name, 0, 1)) }}{{ strtoupper(substr($guest->last_name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        {{ $guest->first_name }} {{ $guest->last_name }}
                        <span class="text-sm font-mono font-medium px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                            #{{ $guest->guest_number }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Guest since {{ $guest->created_at->format('M j, Y') }}
                    </p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('guests.edit', $guest) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-xl transition-colors shadow-sm">
                    Edit Profile
                </a>
                <a href="{{ route('reservations.create', ['guest_id' => $guest->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Booking
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" :dismissible="true" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger" :dismissible="true" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ── Left Column: Details ── --}}
        <div class="space-y-6">
            {{-- Contact Info --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Contact Information</h2>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center gap-3 text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <span class="truncate">{{ $guest->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        <span>{{ $guest->phone }}</span>
                    </div>
                    <div class="flex items-start gap-3 text-slate-600 dark:text-slate-400">
                        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        <div>
                            <p>{{ $guest->address ?? 'No address provided' }}</p>
                            @if($guest->city || $guest->country)
                                <p>{{ $guest->city }}{{ $guest->city && $guest->country ? ', ' : '' }}{{ $guest->country }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <div class="flex items-center justify-between gap-3"><h2 class="text-base font-semibold text-slate-900 dark:text-white">Guest documents</h2><span class="text-xs text-slate-500">PDF or image · max 5 MB</span></div>
                @can('update', $guest)
                    <form method="POST" action="{{ route('guests.documents.store', $guest) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <input type="text" name="name" placeholder="Document name (optional)" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png,.webp" required class="block w-full rounded-xl border border-slate-200 p-2 text-sm dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
                        <x-primary-button>Upload document</x-primary-button>
                    </form>
                @endcan
                <div class="mt-5 space-y-2">
                    @forelse($guest->documents as $document)
                        <a href="{{ route('guests.documents.download', [$guest, $document]) }}" class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm hover:bg-slate-100 dark:bg-slate-900/30 dark:hover:bg-slate-800"><span class="truncate text-slate-700 dark:text-slate-200">{{ $document->name }}</span><span class="ml-3 shrink-0 text-xs text-brand-600 dark:text-brand-400">Download</span></a>
                    @empty
                        <p class="text-sm text-slate-500">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            {{-- Identification --}}
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white mb-4">Identification</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
                        <dt class="text-slate-500 dark:text-slate-400">ID Type</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $guest->id_type ? str_replace('_', ' ', $guest->id_type) : 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-700/50 pb-3">
                        <dt class="text-slate-500 dark:text-slate-400">ID Number</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $guest->id_number ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500 dark:text-slate-400">Nationality</dt>
                        <dd class="font-medium text-slate-900 dark:text-white">{{ $guest->nationality ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ── Right Column: Reservations & History ── --}}
        <div class="xl:col-span-2 space-y-6">
            
            <div class="bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Reservation History</h2>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        {{ $guest->reservations->count() }} Total
                    </span>
                </div>

                @if ($guest->reservations->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-white">No reservations</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">This guest hasn't made any bookings yet.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($guest->reservations->sortByDesc('created_at') as $reservation)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <div class="mb-3 sm:mb-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <a href="{{ route('reservations.show', $reservation) }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline">
                                            {{ $reservation->reservation_number }}
                                        </a>
                                        @php
                                            $statusColors = [
                                                'PENDING'    => 'yellow', 'CONFIRMED'  => 'blue',  'CHECKED_IN' => 'green',
                                                'CHECKED_OUT'=> 'gray',  'CANCELLED'  => 'red',   'NO_SHOW'    => 'orange',
                                            ];
                                        @endphp
                                        <x-status-badge :color="$statusColors[$reservation->status] ?? 'gray'" :text="str_replace('_', ' ', $reservation->status)" />
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                        {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('M j, Y') }} — {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('M j, Y') }}
                                    </p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        Room {{ $reservation->room->room_number ?? 'TBD' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $reservation->branch->name ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
