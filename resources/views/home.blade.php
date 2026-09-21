@extends('layouts.app')

@section('title', 'Prady Technologies | Smart Technology Solutions for African Businesses')
@section('description', 'Prady Technologies builds secure, efficient software solutions for lenders, SACCOs, businesses, property managers, mobility and digital commerce.')

@section('content')
@php
    $solutions = config('portfolio.solutions', []);
    $products = config('portfolio.products', []);
    $trust = config('portfolio.trust', []);
@endphp

{{-- Hero --}}
<section class="mkt-hero" aria-labelledby="home-hero-heading">
    <div class="mkt-container mkt-hero__inner">
        <div class="mkt-hero__copy hero-animate">
            <h1 id="home-hero-heading" class="mkt-hero__title">
                Smart Technology Solutions<br class="hidden sm:block"> for Ambitious Businesses
            </h1>
            <p class="mkt-hero__lead">
                We build secure, efficient software that drives growth.
            </p>
            <div class="mkt-hero__actions">
                <a href="/contact" class="mkt-btn mkt-btn--light">Get Demo →</a>
                <a href="/products" class="mkt-btn mkt-btn--ghost">Our Products</a>
            </div>
        </div>

        <div class="mkt-hero__media hero-animate delay-md">
            <x-marketing.hero-visual />
        </div>
    </div>
</section>

{{-- Our Solutions --}}
<section id="solutions" class="mkt-section mkt-section--white" aria-labelledby="solutions-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="solutions-heading" class="mkt-section__title">Our Solutions</h2>
            <p class="mkt-section__subtitle">
                Tailored systems designed to streamline operations and scale your business.
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

{{-- Product Portfolio --}}
<section id="products" class="mkt-section mkt-section--light" aria-labelledby="products-heading">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 id="products-heading" class="mkt-section__title">Our Product Portfolio</h2>
            <p class="mkt-section__subtitle">
                Practical platforms built for African businesses across finance, mobility, property and commerce.
            </p>
        </div>

        <div class="mkt-products-grid">
            @foreach($products as $product)
                <x-marketing.product-card
                    :name="$product['name']"
                    :short="$product['short']"
                    :icon="$product['icon']"
                    :slug="$product['slug']"
                    :market="$product['market'] ?? null"
                />
            @endforeach
        </div>
    </div>
</section>

{{-- Trust strip --}}
<section class="mkt-trust" aria-label="Why Prady">
    <div class="mkt-container">
        <div class="mkt-trust__grid">
            @foreach($trust as $item)
                <div class="mkt-trust__item">
                    <span class="mkt-trust__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-5 h-5" />
                    </span>
                    <div>
                        <p class="mkt-trust__label">{{ $item['label'] }}</p>
                        <p class="mkt-trust__detail">{{ $item['detail'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- About / business value --}}
<section class="mkt-section mkt-section--white" aria-labelledby="about-heading">
    <div class="mkt-container">
        <div class="mkt-about">
            <div class="mkt-about__copy">
                <h2 id="about-heading" class="mkt-section__title mkt-section__title--left">
                    Technology Built Around Real Businesses
                </h2>
                <p class="mkt-about__text">
                    Prady Technologies develops practical software platforms that help businesses manage operations, serve customers, automate processes and scale with confidence.
                </p>
                <a href="/about" class="mkt-btn mkt-btn--primary">About Prady</a>
            </div>
            <div class="mkt-about__points" role="list">
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="shield" class="w-5 h-5" /></span>
                    <div>
                        <h3>Secure</h3>
                        <p>Controls and safeguards designed into every platform.</p>
                    </div>
                </div>
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="building" class="w-5 h-5" /></span>
                    <div>
                        <h3>Professional</h3>
                        <p>Business software for MFIs, SACCOs, property and commerce.</p>
                    </div>
                </div>
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="handshake" class="w-5 h-5" /></span>
                    <div>
                        <h3>Established</h3>
                        <p>An African technology company focused on real operational needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="mkt-cta" aria-labelledby="cta-heading">
    <div class="mkt-container mkt-cta__inner">
        <h2 id="cta-heading" class="mkt-cta__title">Ready to Transform Your Operations?</h2>
        <p class="mkt-cta__text">Let's discuss the right technology for your business.</p>
        <div class="mkt-cta__actions">
            <a href="/contact" class="mkt-btn mkt-btn--light">Get Demo</a>
            <a href="/contact" class="mkt-btn mkt-btn--ghost">Contact Us</a>
        </div>
    </div>
</section>
@endsection
