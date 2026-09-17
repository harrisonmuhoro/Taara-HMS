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
