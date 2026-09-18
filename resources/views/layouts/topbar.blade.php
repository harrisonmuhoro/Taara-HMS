<header class="min-h-16 md:min-h-20 h-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 flex flex-wrap items-center justify-between px-3 sm:px-4 md:px-8 z-10 sticky top-0 shrink-0">
    
    <!-- Search & Global Actions -->
    <div class="flex-1 flex items-center gap-4 md:gap-6">
        <!-- Mobile Menu Toggle -->
        <button @click="sidebarOpen = true" type="button" aria-label="Open navigation menu" class="lg:hidden p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 rounded-lg">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div class="relative w-full max-w-md hidden md:block">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <form method="GET" action="{{ route('search') }}">
                <label for="global-search" class="sr-only">Search the system</label>
                <input id="global-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search guests, reservations, rooms..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-700 rounded-xl leading-5 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all duration-200">
            </form>
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-xs text-slate-400 border border-slate-200 dark:border-slate-700 rounded px-1.5 py-0.5">⌘K</span>
            </div>
        </div>
    </div>

    <!-- Search remains available on small screens without competing with the action buttons. -->
    <div class="order-3 basis-full md:hidden pb-3 pt-1">
        <form method="GET" action="{{ route('search') }}" class="relative">
            <label for="global-search-mobile" class="sr-only">Search the system</label>
            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input id="global-search-mobile" type="text" name="q" value="{{ request('q') }}" placeholder="Search guests, reservations, rooms..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
        </form>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-1 sm:gap-4">
        
        @php
            $activeBranchName = auth()->user()->branch?->name ?? 'All Branches';
        @endphp
        <div class="hidden sm:flex items-center px-3 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4 mr-2 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            {{ $activeBranchName }}
            <svg class="w-4 h-4 ml-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Theme Toggle -->
        <div x-data="{ darkMode: document.documentElement.classList.contains('dark') }">
            <button
                type="button"
                @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode); localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 rounded-full transition-colors"
                :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="darkMode" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364 1.386l-1.591 1.591M21 12h-2.25m-1.386 6.364l-1.591-1.591M12 18.75V21m-4.773-2.636l-1.591 1.591M5.25 12H3m4.227-5.364L5.636 5.045M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg x-show="!darkMode" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75 9.75 9.75 0 018.25 6c0-1.329.265-2.596.744-3.752A9.753 9.753 0 0012 21.75a9.753 9.753 0 009.752-6.748z" />
                </svg>
            </button>
        </div>

        <!-- Notifications -->
        @php
            $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
            $todayCheckins   = \App\Models\Reservation::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->where('check_in_date', today()->toDateString())->whereIn('status', ['CONFIRMED', 'PENDING'])->count();
            $todayCheckouts  = \App\Models\Reservation::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->where('check_out_date', today()->toDateString())->where('status', 'CHECKED_IN')->count();
            $openMaintenance = \App\Models\MaintenanceTicket::when($branchId, fn ($query) => $query->where('branch_id', $branchId))->whereIn('status', ['open', 'in_progress'])->count();
            $notificationDate = today()->toDateString();

            // Materialize the existing operational alerts so each user can read them once.
            $alertDefinitions = [
                ['key' => 'checkins:' . $notificationDate, 'type' => 'CHECKINS', 'title' => 'Check-ins today', 'message' => $todayCheckins . ' guest check-in' . ($todayCheckins === 1 ? '' : 's') . ' scheduled today.', 'count' => $todayCheckins, 'route' => route('reservations.index')],
                ['key' => 'checkouts:' . $notificationDate, 'type' => 'CHECKOUTS', 'title' => 'Check-outs today', 'message' => $todayCheckouts . ' guest check-out' . ($todayCheckouts === 1 ? '' : 's') . ' scheduled today.', 'count' => $todayCheckouts, 'route' => route('reservations.index')],
                ['key' => 'maintenance:' . $notificationDate, 'type' => 'MAINTENANCE', 'title' => 'Open maintenance', 'message' => $openMaintenance . ' maintenance request' . ($openMaintenance === 1 ? '' : 's') . ' require attention.', 'count' => $openMaintenance, 'route' => route('maintenance.index')],
            ];

            foreach ($alertDefinitions as $alert) {
                if ($alert['count'] > 0) {
                    \App\Models\Notification::firstOrCreate(
                        ['user_id' => auth()->id(), 'type' => $alert['type'] . ':' . $notificationDate],
                        ['title' => $alert['title'], 'message' => $alert['message'], 'reference_type' => $alert['key']]
                    );
                }
            }

            $notifications = \App\Models\Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->whereDate('created_at', $notificationDate)
                ->latest()
                ->get();
            $totalAlerts = $notifications->count();
        @endphp
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button type="button" @click="open = !open" aria-label="Open notifications" class="relative p-2 text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 rounded-full transition-colors">
                @if($totalAlerts > 0)
                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900 text-[9px] font-bold text-white">{{ $totalAlerts > 9 ? '9+' : $totalAlerts }}</span>
                @endif
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </button>
            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-[min(20rem,calc(100vw-1.5rem))] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Today's Alerts</h3>
                    @if($totalAlerts > 0)
                        <div class="flex items-center gap-2"><span class="text-xs text-white bg-red-500 rounded-full px-2 py-0.5 font-semibold">{{ $totalAlerts }} new</span><form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button type="submit" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">Mark all read</button></form></div>
                    @endif
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-72 overflow-y-auto">
                    @forelse($notifications as $notification)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <div class="w-9 h-9 rounded-xl bg-brand-100 dark:bg-brand-900/40 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $notification->title }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $notification->message }}</p>
                                </div>
                            </button>
                        </form>
                    @empty
                        <div class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                            <p class="text-sm">All clear — no alerts today!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Settings Quick Link -->
        <a href="{{ route('profile.edit') }}" aria-label="Open profile settings" title="Profile settings" class="p-2 text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 rounded-full transition-colors">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </a>

        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="p-2 text-slate-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-full transition-colors" title="Logout">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
            </button>
        </form>

    </div>
</header>
