@props(['name' => 'finance', 'class' => 'w-7 h-7'])

@php
    $map = [
        'finance' => 'banknotes',
        'handshake' => 'arrows-right-left',
        'location' => 'map-pin',
        'building' => 'building-office-2',
        'car' => 'truck',
        'live' => 'video-camera',
        'users' => 'user-group',
        'group' => 'users',
        'wallet' => 'credit-card',
        'shield' => 'shield-check',
        'clock' => 'clock',
        'code' => 'code-bracket',
        'hr' => 'identification',
        'piggy' => 'banknotes',
        'cog' => 'cog-6-tooth',
        'bolt' => 'bolt',
        'cloud' => 'cloud',
        'chat' => 'chat-bubble-left-right',
        'check' => 'check-badge',
        'support' => 'lifebuoy',
        'rocket' => 'rocket-launch',
        'search' => 'magnifying-glass',
    ];

    $icon = $map[$name] ?? 'cube';
    $component = 'heroicon-o-' . $icon;
@endphp

<x-dynamic-component :component="$component" {{ $attributes->merge(['class' => $class]) }} />
