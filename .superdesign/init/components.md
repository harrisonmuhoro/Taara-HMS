# Shared UI primitives
### `resources/views/components/nav-link.blade.php`
```blade
@props(['active' => false, 'icon' => 'circle', 'href' => '#'])

@php
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
        'key' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l5.653-4.655m5.546-.952l3.027-3.027a1.5 1.5 0 00-2.121-2.121L9.77 8.093" />',
        'credit-card' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />',
        'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3M16.5 4.205l.75-1.3m-7.5 1.3l-.75-1.3M16.499 19.795l-.75-1.3M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
        'circle' => '<circle cx="12" cy="12" r="3" />',
        'restaurant' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 4.5v-1.5m0 1.5c0 1.34 1.087 2.504 2.5 2.504H9.5M12 14.25c0 1.34 1.087 2.504 2.5 2.504H16.5M3.375 21h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125v-.858c0-.891.563-1.678 1.388-1.978l1.442-.54a2.25 2.25 0 011.5 0l.696.261a3.75 3.75 0 002.608 0l.696-.26a2.25 2.25 0 011.5 0l.696.26a3.75 3.75 0 002.608 0l.696-.261a2.25 2.25 0 011.5 0l1.442.54c.825.3 1.388 1.087 1.388 1.978v.858A1.125 1.125 0 0120.625 21" />',
        'cube' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />',
        'chart-bar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        'user-group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />',
    ];
    $iconPath = $icons[$icon] ?? $icons['circle'];
    
    $baseClasses = 'group flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200';
    $classes = $active
        ? $baseClasses . ' bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400'
        : $baseClasses . ' text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50';
    
    $iconClasses = $active
        ? 'h-5 w-5 text-brand-500'
        : 'h-5 w-5 text-slate-400 group-hover:text-slate-500 dark:group-hover:text-slate-300 transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="{{ $iconClasses }}">
        {!! $iconPath !!}
    </svg>
    <span>{{ $slot }}</span>
    @if($active)
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-brand-500"></span>
    @endif
</a>

```n### `resources/views/components/card.blade.php`
```blade
@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/70']) }}>
    @if ($title)
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $title }}</h2>
    @endif
    @if ($subtitle)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif
    <div class="{{ $title || $subtitle ? 'mt-5' : '' }}">{{ $slot }}</div>
</section>

```n### `resources/views/components/primary-button.blade.php`
```blade
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-600 dark:bg-brand-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-slate-950 uppercase tracking-widest hover:bg-brand-700 dark:hover:bg-brand-400 focus:bg-brand-700 dark:focus:bg-brand-400 active:bg-brand-800 dark:active:bg-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

```n### `resources/views/components/secondary-button.blade.php`
```blade
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

```n### `resources/views/components/danger-button.blade.php`
```blade
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

```n### `resources/views/components/alert.blade.php`
```blade
@props([
    'type' => 'info',   // info | success | warning | danger
    'title' => null,
    'dismissible' => false,
])

@php
    $styles = [
        'info'    => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',    'border' => 'border-blue-400 dark:border-blue-600',   'text' => 'text-blue-800 dark:text-blue-300',   'icon_color' => 'text-blue-500'],
        'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-400 dark:border-emerald-600', 'text' => 'text-emerald-800 dark:text-emerald-300', 'icon_color' => 'text-emerald-500'],
        'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20',  'border' => 'border-amber-400 dark:border-amber-600', 'text' => 'text-amber-800 dark:text-amber-300', 'icon_color' => 'text-amber-500'],
        'danger'  => ['bg' => 'bg-red-50 dark:bg-red-900/20',      'border' => 'border-red-400 dark:border-red-600',     'text' => 'text-red-800 dark:text-red-300',     'icon_color' => 'text-red-500'],
    ];
    $icons = [
        'info'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />',
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
        'danger'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />',
    ];
    $s = $styles[$type] ?? $styles['info'];
    $iconPath = $icons[$type] ?? $icons['info'];
@endphp

<div class="flex gap-3 p-4 rounded-xl border {{ $s['bg'] }} {{ $s['border'] }} {{ $s['text'] }} text-sm" role="alert">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0 {{ $s['icon_color'] }}">
        {!! $iconPath !!}
    </svg>
    <div class="flex-1">
        @if($title)
            <p class="font-semibold mb-1">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if($dismissible)
        <button type="button" onclick="this.closest('[role=alert]').remove()" class="ml-auto -mx-1.5 -my-1.5 p-1.5 inline-flex items-center justify-center rounded-lg opacity-60 hover:opacity-100 transition-opacity">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>

```n### `resources/views/components/status-badge.blade.php`
```blade
@props([
    'color' => 'gray',
    'text' => '',
    'dot' => true,
])

@php
    $palettes = [
        'green'  => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20',
        'blue'   => 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20',
        'red'    => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20',
        'yellow' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-500 dark:ring-amber-400/20',
        'sky'    => 'bg-sky-50 text-sky-700 ring-sky-700/10 dark:bg-sky-400/10 dark:text-sky-400 dark:ring-sky-400/20',
        'teal'   => 'bg-teal-50 text-teal-700 ring-teal-700/10 dark:bg-teal-400/10 dark:text-teal-400 dark:ring-teal-400/20',
        'orange' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-400/10 dark:text-orange-400 dark:ring-orange-400/20',
        'gray'   => 'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
        'slate'  => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-700/40 dark:text-slate-300 dark:ring-slate-500/20',
    ];
    $dotColors = [
        'green' => 'fill-emerald-500', 'blue' => 'fill-blue-500', 'red' => 'fill-red-500',
        'yellow' => 'fill-amber-500', 'sky' => 'fill-sky-500', 'teal' => 'fill-teal-500',
        'orange' => 'fill-orange-500', 'gray' => 'fill-gray-400', 'slate' => 'fill-slate-400',
    ];
    $cls = $palettes[$color] ?? $palettes['gray'];
    $dotCls = $dotColors[$color] ?? $dotColors['gray'];
@endphp

<span class="inline-flex items-center gap-x-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $cls }}">
    @if($dot)
        <svg class="h-1.5 w-1.5 {{ $dotCls }}" viewBox="0 0 6 6" aria-hidden="true">
            <circle cx="3" cy="3" r="3" />
        </svg>
    @endif
    {{ $text ?: $slot }}
</span>

```n### `resources/views/components/text-input.blade.php`
```blade
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-500 dark:focus:border-brand-600 focus:ring-brand-500 dark:focus:ring-brand-600 rounded-md shadow-sm']) }}>

```n### `resources/views/components/input-label.blade.php`
```blade
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>

```n### `resources/views/components/breadcrumb.blade.php`
```blade
@props(['links' => []])

{{-- $links: array of ['label' => '...', 'url' => '...'] - last item is current page --}}
<nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
        </svg>
    </a>

    @foreach ($links as $link)
        <svg class="h-4 w-4 text-slate-300 dark:text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        @if ($loop->last)
            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $link['label'] }}</span>
        @else
            <a href="{{ $link['url'] }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">{{ $link['label'] }}</a>
        @endif
    @endforeach
</nav>

```n
