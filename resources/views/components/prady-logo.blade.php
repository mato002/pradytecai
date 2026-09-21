@props([
    'variant' => 'nav', // nav | footer | mark
    'theme' => 'light',  // light | dark
])

@php
    $isDark = $theme === 'dark';
    $navy = $isDark ? '#FFFFFF' : '#0B2347';
    $muted = $isDark ? 'rgba(255,255,255,0.8)' : '#1B3A5F';
@endphp

<a href="/" {{ $attributes->merge(['class' => 'prady-brand group inline-flex items-center gap-2.5 no-underline']) }} aria-label="Prady Technologies Ltd — Home">
    <span class="prady-brand__mark relative shrink-0 w-10 h-10" aria-hidden="true">
        <svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6h28v16H22v8H6V6z" fill="{{ $navy }}"/>
            <path d="M22 22h16v16H22V22z" fill="{{ $navy }}"/>
            <rect x="28" y="28" width="14" height="14" fill="#00AEEF"/>
        </svg>
    </span>

    @if($variant !== 'mark')
        <span class="flex flex-col min-w-0">
            <span class="prady-brand__name font-extrabold uppercase leading-none tracking-wide text-[1.05rem] sm:text-[1.15rem]" style="color: {{ $navy }}; font-family: 'Montserrat', system-ui, sans-serif;">
                PR<span class="relative inline-block">A<span class="absolute left-[18%] right-[18%] top-[52%] h-[2.5px] rounded-sm" style="background:#00AEEF;"></span></span>DY
            </span>
            <span class="font-semibold uppercase leading-tight tracking-[0.12em] text-[0.58rem] sm:text-[0.62rem] mt-1" style="color: {{ $muted }}; font-family: 'Montserrat', system-ui, sans-serif;">
                Technologies Ltd
            </span>
            @if($variant === 'nav')
                <span class="hidden lg:block font-medium uppercase tracking-[0.2em] text-[0.5rem] mt-1.5 pt-1 border-t" style="color: {{ $muted }}; border-color: transparent; border-image: linear-gradient(90deg, #00AEEF, #0B2347) 1; font-family: 'Montserrat', system-ui, sans-serif;">
                    Doing It Differently
                </span>
            @endif
        </span>
    @endif
</a>
