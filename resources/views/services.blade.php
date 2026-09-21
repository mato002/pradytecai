@extends('layouts.app')

@section('title', 'Industries — Prady Technologies Ltd')
@section('description', 'Prady Technologies serves microfinance, SACCOs, logistics, property, automotive, social commerce, and tourism markets across Kenya and beyond.')

@section('content')
@php
    $industries = [
        ['title' => 'Microfinance & Lending', 'desc' => 'Operating systems for MFIs, credit companies, and digital lenders — loans, collections, M-Pesa, and controls.', 'icon' => 'finance', 'markets' => 'MFIs · Credit companies · Lenders'],
        ['title' => 'SACCOs & Member Finance', 'desc' => 'Membership, savings, loans, governance, and administration for SACCOs and member-based institutions.', 'icon' => 'users', 'markets' => 'SACCOs · Cooperatives'],
        ['title' => 'Investment Groups & Chamas', 'desc' => 'Transparent group accounts, contributions, welfare, loans, and project tracking for chamas and clubs.', 'icon' => 'group', 'markets' => 'Chamas · Welfare groups'],
        ['title' => 'Fleet & Logistics', 'desc' => 'Affordable GPS hosting and tracking for vehicle owners, fleets, and resellers who need reliability.', 'icon' => 'location', 'markets' => 'Fleets · Logistics · Resellers'],
        ['title' => 'Property & Real Estate', 'desc' => 'Units, tenants, leases, rent, arrears, and maintenance in one property performance platform.', 'icon' => 'building', 'markets' => 'Landlords · Property managers'],
        ['title' => 'Automotive Commerce', 'desc' => 'Vehicle-first spare parts matching, dealer POS, mechanics, and logistics through SpareMe.', 'icon' => 'car', 'markets' => 'Dealers · Garages · Owners'],
        ['title' => 'Social Commerce', 'desc' => 'Live selling platforms for creators and SMEs — announce shows, convert audiences, keep products live after broadcast.', 'icon' => 'live', 'markets' => 'Creators · SMEs · Merchants'],
        ['title' => 'Tourism & Travel Finance', 'desc' => 'Mtalii Travel Wallet for tourist payments, FX, and local merchant acceptance.', 'icon' => 'wallet', 'markets' => 'Tourists · Operators · Merchants'],
    ];
@endphp

<x-marketing.page-hero
    title="Industries We Serve"
    subtitle="Purpose-built platforms for the markets where Prady Technologies is building deep domain software."
    :breadcrumbs="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Industries'],
    ]"
/>

<section class="mkt-section mkt-section--light">
    <div class="mkt-container">
        <div class="mkt-products-grid">
            @foreach($industries as $item)
                <article class="mkt-product-card">
                    <span class="mkt-product-card__icon" aria-hidden="true">
                        <x-prady-icon :name="$item['icon']" class="w-7 h-7" />
                    </span>
                    <h3 class="mkt-product-card__title">{{ $item['title'] }}</h3>
                    <p class="mkt-product-card__text">{{ $item['desc'] }}</p>
                    <p class="mkt-product-card__market">{{ $item['markets'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="mkt-section mkt-section--white">
    <div class="mkt-container">
        <div class="mkt-section__header">
            <h2 class="mkt-section__title">How we deliver</h2>
            <p class="mkt-section__subtitle">End-to-end services around every Prady platform.</p>
        </div>
        <div class="mkt-solutions-grid">
            @foreach([
                ['Custom Development', 'code', 'Bespoke modules and workflows for your institution.'],
                ['Cloud Infrastructure', 'shield', 'Secure hosting, monitoring, and uptime operations.'],
                ['Integrations', 'handshake', 'M-Pesa, payments, GPS devices, and third-party APIs.'],
                ['Training & Support', 'hr', 'Onboarding, documentation, and ongoing support.'],
            ] as $svc)
                <div class="mkt-solution-card" style="cursor:default;pointer-events:none;">
                    <span class="mkt-solution-card__icon" aria-hidden="true"><x-prady-icon :name="$svc[1]" class="w-8 h-8" /></span>
                    <h3 class="mkt-solution-card__title">{{ $svc[0] }}</h3>
                    <p class="mkt-solution-card__text">{{ $svc[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<x-marketing.cta
    title="Ready to get started?"
    text="Let’s discuss how a Prady platform can fit your industry."
    primary-label="Contact Us"
    secondary-label="View Products"
    secondary-href="/products"
/>
@endsection
