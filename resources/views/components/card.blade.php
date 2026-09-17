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
