# Layouts

Shared application shell. Primary entry is `x-app-layout`.
### `resources/views/components/app-layout.blade.php`
```blade
@props(['title' => null, 'header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            (() => {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', savedTheme ? savedTheme === 'dark' : prefersDark);
            })();
        </script>
        <title>{{ $title ? $title . ' Â· ' . config('app.name') : config('app.name', 'Hotel MS') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'DM Sans', sans-serif; }
            h1, h2, h3, h4, h5, h6 { font-family: 'Cormorant Garamond', serif; }
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.25); border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.45); }
            @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in-down { animation: fadeInDown 0.4s ease both; }
            .animate-fade-in-up { animation: fadeInUp 0.4s ease both; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#0B0F1A] text-slate-900 dark:text-slate-100 overflow-hidden flex h-screen" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>
        
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-2/3 h-96 bg-brand-600/5 pointer-events-none"></div>
            
            @include('layouts.topbar')

            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                @isset($header)
                    <header class="mb-8 animate-fade-in-down">{{ $header }}</header>
                @endisset
                <div class="animate-fade-in-up">{{ $slot }}</div>
            </main>
        </div>
    </body>
</html>

```n### `resources/views/layouts/sidebar.blade.php`
```blade
<aside class="w-72 bg-white dark:bg-[#0D1220] border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-300 z-30 shrink-0 fixed inset-y-0 left-0 transform lg:static lg:translate-x-0" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
    <!-- Logo Area -->
    <div class="h-16 md:h-20 flex items-center justify-between px-7 border-b border-slate-200 dark:border-slate-800 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold text-xl group-hover:scale-105 transition-all duration-300">
                H
            </div>
            <div>
                <span class="block font-normal text-xl tracking-tight text-slate-800 dark:text-white leading-none" style="font-family: 'Cormorant Garamond', serif;">
                    Grand<span class="text-brand-500 font-light">Hotel</span>
                </span>
                <span class="block text-[10px] uppercase tracking-[0.16em] text-slate-400 dark:text-slate-500">Property Management</span>
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-0.5">
        {{-- Overview --}}
        @can('dashboard.view')
            <div class="text-xs font-semibold text-slate-400 dark:text-slate-600 uppercase tracking-widest mb-2 px-3 mt-2">Overview</div>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Dashboard</x-nav-link>
        @endcan

        {{-- Front Desk --}}
        @canany(['reservations.view', 'guests.view', 'rooms.view'])
            <div class="text-xs font-semibold text-slate-400 dark:text-slate-600 uppercase tracking-widest mb-2 px-3 mt-6">Front Desk</div>
            @can('reservations.view')
                <x-nav-link :href="route('reservations.index')" :active="request()->routeIs('reservations.*')" icon="calendar">Reservations</x-nav-link>
            @endcan
            @can('stays.check_in')
                <x-nav-link :href="route('front-desk.check-in')" :active="request()->routeIs('front-desk.check-in*')" icon="login">Check-in</x-nav-link>
            @endcan
            @can('stays.check_out')
                <x-nav-link :href="route('front-desk.check-out')" :active="request()->routeIs('front-desk.check-out*')" icon="logout">Check-out</x-nav-link>
            @endcan
            @can('guests.view')
                <x-nav-link :href="route('guests.index')" :active="request()->routeIs('guests.*')" icon="users">Guests</x-nav-link>
            @endcan
            @can('rooms.view')
                <x-nav-link :href="route('rooms.index')" :active="request()->routeIs('rooms.*')" icon="key">Rooms &amp; Status</x-nav-link>
            @endcan
        @endcanany

        {{-- Operations --}}
        @canany(['housekeeping.view', 'maintenance.view', 'restaurant.view', 'inventory.view'])
            <div class="text-xs font-semibold text-slate-400 dark:text-slate-600 uppercase tracking-widest mb-2 px-3 mt-6">Operations</div>
            @can('housekeeping.view')
                <x-nav-link :href="route('housekeeping.index')" :active="request()->routeIs('housekeeping.*')" icon="sparkles">Housekeeping</x-nav-link>
            @endcan
            @can('maintenance.view')
                <x-nav-link :href="route('maintenance.index')" :active="request()->routeIs('maintenance.*')" icon="wrench">Maintenance</x-nav-link>
            @endcan

            @can('inventory.view')
                <x-nav-link :href="route('inventory.products.index')" :active="request()->routeIs('inventory.*')" icon="cube">Inventory</x-nav-link>
            @endcan
        @endcanany

        {{-- Administration --}}
        @canany(['invoices.view', 'payments.view', 'reports.view', 'users.view', 'roles.manage', 'settings.view', 'audit_logs.view'])
            <div class="text-xs font-semibold text-slate-400 dark:text-slate-600 uppercase tracking-widest mb-2 px-3 mt-6">Administration</div>
            @canany(['invoices.view', 'payments.view'])
                <x-nav-link :href="route('finance.invoices')" :active="request()->routeIs('finance.*')" icon="credit-card">Financials</x-nav-link>
            @endcanany
            @can('reports.view')
                <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="chart-bar">Reports</x-nav-link>
            @endcan
            @canany(['users.view', 'roles.manage'])
                <x-nav-link :href="route('staff.index')" :active="request()->routeIs('staff.*') || request()->routeIs('roles.*')" icon="user-group">Staff &amp; Roles</x-nav-link>
            @endcanany
            @can('settings.view')
                <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')" icon="cog">Settings</x-nav-link>
                <x-nav-link :href="route('configuration.index')" :active="request()->routeIs('configuration.*')" icon="cog">Hotel Configuration</x-nav-link>
            @endcan
            @can('audit_logs.view')
                <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')" icon="clipboard-document-list">Audit Logs</x-nav-link>
            @endcan
        @endcanany

        {{-- Restaurant --}}
        @canany(['restaurant.view', 'restaurant.manage'])
            <div class="text-xs font-semibold text-slate-400 dark:text-slate-600 uppercase tracking-widest mb-2 px-3 mt-6">Restaurant</div>
            @can('restaurant.view')
                <x-nav-link :href="route('restaurant.pos')" :active="request()->routeIs('restaurant.pos')" icon="shopping-cart">POS Terminal</x-nav-link>
                <x-nav-link :href="route('restaurant.orders')" :active="request()->routeIs('restaurant.orders')" icon="clipboard-document-list">Restaurant Orders</x-nav-link>
            @endcan
            @can('restaurant.manage')
                <x-nav-link :href="route('restaurant.menu.index')" :active="request()->routeIs('restaurant.menu.*')" icon="book-open">Menu Management</x-nav-link>
            @endcan
        @endcanany

    </nav>

    <!-- User Profile Snippet (Bottom) -->
    <div class="p-3 border-t border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group">
            <div class="w-9 h-9 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 overflow-hidden">
                <p class="text-sm font-semibold text-slate-900 dark:text-white truncate leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-400 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
            </svg>
        </div>
    </div>
</aside>

```n### `resources/views/layouts/topbar.blade.php`
```blade
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
                <span class="text-xs text-slate-400 border border-slate-200 dark:border-slate-700 rounded px-1.5 py-0.5">âŒ˜K</span>
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
                            <p class="text-sm">All clear â€” no alerts today!</p>
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

```n### `resources/views/layouts/guest.blade.php`
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400,0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

```n
