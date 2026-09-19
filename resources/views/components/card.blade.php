@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'card p-5']) }}>
    @if ($title)
        <h2 class="text-lg font-semibold text-ink dark:text-[#F0E6D8]">{{ $title }}</h2>
    @endif
    @if ($subtitle)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif
    <div class="{{ $title || $subtitle ? 'mt-5' : '' }}">{{ $slot }}</div>
</section>
