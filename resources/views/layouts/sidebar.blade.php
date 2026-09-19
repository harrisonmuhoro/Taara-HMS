<aside class="no-print fixed inset-y-0 left-0 z-30 flex w-64 shrink-0 transform flex-col border-r border-[#D9CFC0] bg-surface dark:border-[#3A3228] dark:bg-surface-dark lg:static lg:translate-x-0" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
    <div class="flex h-14 shrink-0 items-center justify-between border-b border-[#D9CFC0] px-5 dark:border-[#3A3228]">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <img src="{{ asset('grand-horizon-mark.svg') }}" alt="Grand Horizon" class="h-9 w-9 shrink-0 rounded-lg">
            <span class="min-w-0">
                <span class="font-display block text-lg leading-tight text-ink dark:text-[#F0E6D8]">Grand Horizon</span>
                <span class="block text-xs text-ink-muted">Nairobi · Property desk</span>
            </span>
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
            <div class="nav-section-label mt-1">Overview</div>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Dashboard</x-nav-link>
        @endcan

        {{-- Front Desk --}}
        @canany(['reservations.view', 'guests.view', 'rooms.view'])
            <div class="nav-section-label mt-5">Front desk</div>
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
            <div class="nav-section-label mt-5">Operations</div>
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
            <div class="nav-section-label mt-5">Administration</div>
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
            <div class="nav-section-label mt-5">Restaurant</div>
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
    <div class="border-t border-[#D9CFC0] p-3 dark:border-[#3A3228]">
        <div class="flex items-center gap-3 p-2">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-brand-500 text-sm font-semibold text-white">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold leading-tight text-ink dark:text-[#F0E6D8]">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-ink-muted">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
