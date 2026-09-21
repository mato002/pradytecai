@extends('layouts.app')

@section('title', 'Prady Technologies | Smart Technology Solutions for African Businesses')
@section('description', 'Prady Technologies builds secure, efficient software solutions for lenders, SACCOs, businesses, property managers, mobility and digital commerce.')

@section('content')
@php
    $solutions = config('portfolio.solutions', []);
    $products = collect(config('portfolio.products', []))->keyBy('slug');
    $groups = config('portfolio.product_groups', []);
    $trust = config('portfolio.trust', []);
    $manage = config('portfolio.manage', []);
    $capabilities = config('portfolio.capabilities', []);
    $industries = config('portfolio.industries', []);
    $featuredMeta = config('portfolio.featured', []);
    $featured = $products->get($featuredMeta['slug'] ?? 'prady-microfinance');
    $featuredOutcomes = $featuredMeta['outcomes'] ?? [];
@endphp

{{-- PHASE 2: Reference hero --}}
<section class="mkt-hero" aria-labelledby="home-hero-heading">
    <div class="mkt-container mkt-hero__inner">
        <div class="mkt-hero__copy">
            <h1 id="home-hero-heading" class="mkt-hero__title">
                <span class="mkt-hero__title-line">Smart Technology Solutions</span>
                <span class="mkt-hero__title-line">for Ambitious Businesses</span>
            </h1>
            <p class="mkt-hero__lead">
                We build secure, efficient software that drives growth.
            </p>
            <div class="mkt-hero__actions">
                <a href="/contact" class="mkt-btn mkt-btn--light mkt-btn--hero">Get Demo →</a>
                <a href="/products" class="mkt-btn mkt-btn--ghost mkt-btn--hero">Our Products</a>
            </div>
        </div>

        <div class="mkt-hero__media" aria-hidden="true">
            <x-marketing.hero-visual />
        </div>
    </div>
</section>

{{-- PHASE 3: Reference solutions --}}
<section id="solutions" class="mkt-section mkt-section--solutions" aria-labelledby="solutions-heading">
    <div class="mkt-container">
        <div class="mkt-section__header mkt-section__header--solutions">
            <h2 id="solutions-heading" class="mkt-section__title">Our Solutions</h2>
            <p class="mkt-section__subtitle">
                Tailored systems designed to streamline operations and scale your business
            </p>
        </div>

        <div class="mkt-solutions-grid">
            @foreach($solutions as $solution)
                <x-marketing.solution-card
                    :name="$solution['name']"
                    :short="$solution['short']"
                    :icon="$solution['icon']"
                    :href="$solution['href']"
                />
            @endforeach
        </div>
    </div>
</section>

<section class="mkt-trust" aria-label="Why Prady">
    <div class="mkt-container">
        <div class="mkt-trust__grid mkt-trust__grid--three">
            @foreach($trust as $item)
                <div class="mkt-trust__item mkt-trust__item--center">
                    <span class="mkt-trust__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-5 h-5" />
                    </span>
                    <p class="mkt-trust__label">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PHASE 4: Discovery --}}
<section id="manage" class="mkt-section mkt-section--light" aria-labelledby="manage-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="manage-heading" class="mkt-section__title">What do you want to manage?</h2>
            <p class="mkt-section__subtitle">Guide visitors directly to the right Prady platform.</p>
        </div>
        <div class="mkt-manage-grid">
            @foreach($manage as $item)
                <a href="{{ $item['href'] }}" class="mkt-manage-card">
                    <span class="mkt-manage-card__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-6 h-6" />
                    </span>
                    <span class="mkt-manage-card__label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section id="products" class="mkt-section mkt-section--white" aria-labelledby="products-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="products-heading" class="mkt-section__title">Product Portfolio</h2>
            <p class="mkt-section__subtitle">
                Practical platforms built for African businesses across finance, mobility, property and commerce.
            </p>
        </div>

        @foreach($groups as $group)
            <div class="mkt-product-group">
                <h3 class="mkt-product-group__title">{{ $group['title'] }}</h3>
                <div class="mkt-products-grid">
                    @foreach($group['slugs'] as $slug)
                        @if($product = $products->get($slug))
                            <x-marketing.product-card
                                :name="$product['name']"
                                :short="$product['short']"
                                :icon="$product['icon']"
                                :slug="$product['slug']"
                                :market="$product['market'] ?? null"
                            />
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<section id="capabilities" class="mkt-section mkt-section--light" aria-labelledby="capabilities-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="capabilities-heading" class="mkt-section__title">Solutions &amp; Capabilities</h2>
            <p class="mkt-section__subtitle">Services that support every Prady platform — separate from our named products.</p>
        </div>
        <div class="mkt-products-grid">
            @foreach($capabilities as $item)
                <a href="{{ $item['href'] }}" class="mkt-product-card">
                    <span class="mkt-product-card__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-7 h-7" />
                    </span>
                    <h3 class="mkt-product-card__title">{{ $item['name'] }}</h3>
                    <p class="mkt-product-card__text">{{ $item['short'] }}</p>
                    <span class="mkt-product-card__link">Learn more <span aria-hidden="true">→</span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section id="industries" class="mkt-section mkt-section--white" aria-labelledby="industries-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="industries-heading" class="mkt-section__title">Industries We Serve</h2>
            <p class="mkt-section__subtitle">Purpose-built platforms for the markets where Prady Technologies works deepest.</p>
        </div>
        <div class="mkt-products-grid">
            @foreach($industries as $item)
                <a href="{{ $item['href'] }}" class="mkt-product-card">
                    <span class="mkt-product-card__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-7 h-7" />
                    </span>
                    <h3 class="mkt-product-card__title">{{ $item['name'] }}</h3>
                    <p class="mkt-product-card__text">{{ $item['short'] }}</p>
                    <span class="mkt-product-card__link">See products <span aria-hidden="true">→</span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

@if($featured)
<section id="featured" class="mkt-section mkt-section--light" aria-labelledby="featured-heading">
    <div class="mkt-container">
        <div class="mkt-featured">
            <div class="mkt-featured__visual" aria-hidden="true">
                <x-marketing.hero-visual />
            </div>
            <div class="mkt-featured__copy">
                <p class="mkt-featured__eyebrow">Featured product</p>
                <h2 id="featured-heading" class="mkt-section__title mkt-section__title--left">{{ $featured['name'] }}</h2>
                <p class="mkt-about__text">{{ $featured['description'] }}</p>
                <ul class="mkt-featured__outcomes">
                    @foreach($featuredOutcomes as $outcome)
                        <li>
                            <span aria-hidden="true"><x-prady-icon name="check" class="w-5 h-5" /></span>
                            <span>{{ $outcome }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mkt-featured__actions">
                    <a href="/products#{{ $featured['slug'] }}" class="mkt-btn mkt-btn--primary">Explore Product</a>
                    <a href="/contact?product={{ urlencode($featured['name']) }}" class="mkt-btn mkt-btn--outline">Request Demo</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="mkt-cta" aria-labelledby="cta-heading">
    <div class="mkt-container mkt-cta__inner">
        <h2 id="cta-heading" class="mkt-cta__title">Not sure which Prady platform fits your business?</h2>
        <p class="mkt-cta__text">Tell us what you're trying to manage and we'll guide you to the right solution.</p>
        <div class="mkt-cta__actions">
            <a href="/contact" class="mkt-btn mkt-btn--light">Talk to Us</a>
            <a href="/contact" class="mkt-btn mkt-btn--ghost">Get Demo</a>
        </div>
    </div>
</section>
@endsection
