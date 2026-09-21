@props([
    'name',
    'short',
    'icon' => 'finance',
    'slug',
    'market' => null,
])

<article {{ $attributes->merge(['class' => 'mkt-product-card', 'id' => $slug]) }}>
    <span class="mkt-product-card__icon" aria-hidden="true">
        <x-prady-icon :name="$icon" class="w-7 h-7" />
    </span>
    <h3 class="mkt-product-card__title">{{ $name }}</h3>
    <p class="mkt-product-card__text">{{ $short }}</p>
    @if($market)
        <p class="mkt-product-card__market">{{ $market }}</p>
    @endif
    <a href="/products#{{ $slug }}" class="mkt-product-card__link">
        Learn More
        <span aria-hidden="true">→</span>
    </a>
</article>
