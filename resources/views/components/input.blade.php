@props(['label' => null])

<div>
    @if ($label)<x-input-label :value="$label" />@endif
    <x-text-input {{ $attributes->merge(['class' => 'mt-1 block w-full']) }} />
</div>
