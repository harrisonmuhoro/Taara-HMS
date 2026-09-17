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
