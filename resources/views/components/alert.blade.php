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
