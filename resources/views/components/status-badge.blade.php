@props([
    'color' => 'gray',
    'text' => '',
    'dot' => true,
])

@php
    $palettes = [
        'green'  => 'bg-olive-50 text-olive-800 ring-olive-600/20 dark:bg-olive-900/40 dark:text-olive-300 dark:ring-olive-400/20',
        'blue'   => 'bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-900/40 dark:text-brand-300 dark:ring-brand-400/20',
        'red'    => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20',
        'yellow' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-500 dark:ring-amber-400/20',
        'sky'    => 'bg-sky-50 text-sky-700 ring-sky-700/10 dark:bg-sky-400/10 dark:text-sky-400 dark:ring-sky-400/20',
        'teal'   => 'bg-teal-50 text-teal-700 ring-teal-700/10 dark:bg-teal-400/10 dark:text-teal-400 dark:ring-teal-400/20',
        'orange' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-400/10 dark:text-orange-400 dark:ring-orange-400/20',
        'gray'   => 'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
        'slate'  => 'bg-slate-100 text-slate-700 ring-slate-600/10 dark:bg-slate-700/40 dark:text-slate-300 dark:ring-slate-500/20',
    ];
    $dotColors = [
        'green' => 'fill-olive-600', 'blue' => 'fill-brand-500', 'red' => 'fill-red-500',
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
