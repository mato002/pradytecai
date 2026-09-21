@props([
    'name',
    'short',
    'icon' => 'finance',
    'href' => '/products',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'mkt-solution-card']) }}>
    <span class="mkt-solution-card__icon" aria-hidden="true">
        <x-prady-icon :name="$icon" class="w-7 h-7" />
    </span>
    <h3 class="mkt-solution-card__title">{{ $name }}</h3>
    <p class="mkt-solution-card__text">{{ $short }}</p>
</a>
