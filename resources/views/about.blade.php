@extends('layouts.app')

@section('title', 'About Us — Prady Technologies Ltd')
@section('description', 'Learn about Prady Technologies Ltd — doing it differently with secure, efficient software for ambitious businesses.')

@section('content')
<x-marketing.page-hero
    title="About Prady Technologies"
    subtitle="Doing it differently — smart technology solutions for ambitious businesses"
    :breadcrumbs="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'About Us'],
    ]"
/>

<section class="mkt-section mkt-section--white">
    <div class="mkt-container">
        <div class="mkt-about">
            <div class="mkt-about__copy">
                <h2 class="mkt-section__title mkt-section__title--left">Our Mission</h2>
                <p class="mkt-about__text">
                    At Prady Technologies Ltd, we deliver practical software platforms that empower businesses to achieve their goals. We combine solid engineering with deep industry expertise to create systems that drive real operational value.
                </p>
                <p class="mkt-about__text">
                    Our mission is to make enterprise-grade software accessible to institutions of all sizes — helping them streamline operations, serve customers better, and scale with confidence.
                </p>
            </div>
            <div class="mkt-about__points" role="list">
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="shield" class="w-5 h-5" /></span>
                    <div>
                        <h3>Secure platforms</h3>
                        <p>Controls and safeguards designed into every product.</p>
                    </div>
                </div>
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="building" class="w-5 h-5" /></span>
                    <div>
                        <h3>Business-focused</h3>
                        <p>Built for MFIs, SACCOs, property, mobility and commerce.</p>
                    </div>
                </div>
                <div class="mkt-about__point" role="listitem">
                    <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="handshake" class="w-5 h-5" /></span>
                    <div>
                        <h3>Long-term partners</h3>
                        <p>Ongoing support and expertise beyond the initial launch.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mkt-section mkt-section--light">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 class="mkt-section__title">Our Values</h2>
            <p class="mkt-section__subtitle">The principles that guide everything we build.</p>
        </div>
        <div class="mkt-products-grid">
            <article class="mkt-product-card">
                <span class="mkt-product-card__icon" aria-hidden="true"><x-prady-icon name="shield" class="w-7 h-7" /></span>
                <h3 class="mkt-product-card__title">Reliability</h3>
                <p class="mkt-product-card__text">We build robust, secure systems you can depend on for day-to-day operations.</p>
            </article>
            <article class="mkt-product-card">
                <span class="mkt-product-card__icon" aria-hidden="true"><x-prady-icon name="bolt" class="w-7 h-7" /></span>
                <h3 class="mkt-product-card__title">Innovation</h3>
                <p class="mkt-product-card__text">We continuously improve our platforms with technology that benefits our clients.</p>
            </article>
            <article class="mkt-product-card">
                <span class="mkt-product-card__icon" aria-hidden="true"><x-prady-icon name="handshake" class="w-7 h-7" /></span>
                <h3 class="mkt-product-card__title">Partnership</h3>
                <p class="mkt-product-card__text">Your success is our success — we support you at every stage of growth.</p>
            </article>
        </div>
    </div>
</section>

<section class="mkt-section mkt-section--white">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 class="mkt-section__title">Our Expertise</h2>
            <p class="mkt-section__subtitle">Specialized knowledge across the markets we serve.</p>
        </div>
        <div class="mkt-solutions-grid">
            @foreach([
                ['chat', 'Communication', 'SMS, WhatsApp and messaging platforms'],
                ['finance', 'Finance', 'Microfinance and member-based systems'],
                ['shield', 'Security', 'Enterprise controls and compliance focus'],
                ['cloud', 'Cloud', 'Reliable hosting and scalable delivery'],
            ] as [$icon, $title, $text])
                <div class="mkt-solution-card" style="cursor:default;pointer-events:none;">
                    <span class="mkt-solution-card__icon" aria-hidden="true"><x-prady-icon :name="$icon" class="w-8 h-8" /></span>
                    <h3 class="mkt-solution-card__title">{{ $title }}</h3>
                    <p class="mkt-solution-card__text">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-marketing.cta
    title="Want to Learn More?"
    text="Get in touch to discuss how we can help transform your operations."
    primary-label="Contact Us"
    secondary-label="Our Products"
    secondary-href="/products"
/>
@endsection
