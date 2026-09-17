@props(['label' => null])

<div>
    @if ($label)<x-input-label :value="$label" />@endif
    <textarea {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300']) }}>{{ $slot }}</textarea>
</div>
