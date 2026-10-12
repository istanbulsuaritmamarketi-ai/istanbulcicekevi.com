@props(['active', 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 bg-rose-700 text-white rounded-xl font-bold shadow-xs transition duration-200 whitespace-nowrap'
            : 'flex items-center px-4 py-2.5 text-rose-100/80 hover:bg-rose-900/60 hover:text-white rounded-xl transition duration-200 whitespace-nowrap';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} mr-3 w-5 text-center"></i>
    @endif
    {{ $slot }}
</a>
