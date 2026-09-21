@props([
    'variant' => 'nav', // nav | footer | mark
    'theme' => 'light',  // light | dark
])

@php
    $isDark = $theme === 'dark';
    $navy = $isDark ? '#FFFFFF' : '#053171';
    $muted = $isDark ? 'rgba(255,255,255,0.72)' : '#535B67';
    $cyan = '#19A7EF';
@endphp

<a href="/" {{ $attributes->merge(['class' => 'prady-brand group inline-flex items-center gap-3 no-underline']) }} aria-label="Prady Technologies Ltd — Home">
    <span class="prady-brand__mark relative shrink-0" aria-hidden="true">
        <svg class="prady-brand__mark-svg" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6h28v16H22v8H6V6z" fill="{{ $navy }}"/>
            <path d="M22 22h16v16H22V22z" fill="{{ $navy }}"/>
            <rect x="28" y="28" width="14" height="14" fill="{{ $cyan }}"/>
        </svg>
    </span>

    @if($variant !== 'mark')
        <span class="prady-brand__text flex flex-col min-w-0">
            <span class="prady-brand__name" style="color: {{ $navy }};">
                PR<span class="relative inline-block">A<span class="prady-brand__a-bar" style="background:{{ $cyan }};"></span></span>DY
            </span>
            <span class="prady-brand__sub" style="color: {{ $muted }};">Technologies Ltd</span>
            @if($variant === 'nav')
                <span class="prady-brand__tagline" style="color: {{ $muted }};">Doing It Differently</span>
            @endif
        </span>
    @endif
</a>
