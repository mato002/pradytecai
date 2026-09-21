@extends('layouts.app')

@section('title', 'Products — Prady Technologies Ltd')
@section('description', 'Explore the Prady Technologies product portfolio: Microfinance, Rafiki Loan, GPS Tracking, Property Management, SpareMe, Live Commerce, SACCO, Chama, and Mtalii Travel Wallet.')

@section('content')
@php $products = config('portfolio.products'); @endphp

<x-marketing.page-hero
    title="Product Portfolio"
    subtitle="Platforms built for finance, mobility, property, commerce and tourism."
    :breadcrumbs="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Products'],
    ]"
/>

<section class="mkt-section mkt-section--light">
    <div class="mkt-container space-y-6">
        @foreach($products as $index => $product)
            <article id="{{ $product['slug'] }}" class="mkt-product-card scroll-mt-28 !flex-row !items-stretch !p-0 overflow-hidden">
                <div class="grid lg:grid-cols-12 gap-0 w-full">
                    <div class="lg:col-span-8 p-7 sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[var(--prady-sky)] mb-2">
                            {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} · Product
                        </p>
                        <div class="flex items-start gap-4 mb-3">
                            <span class="mkt-product-card__icon !mb-0 shrink-0" aria-hidden="true">
                                <x-prady-icon :name="$product['icon']" class="w-7 h-7" />
                            </span>
                            <h2 class="mkt-product-card__title !mb-0 text-2xl sm:text-[1.7rem]">{{ $product['name'] }}</h2>
                        </div>
                        <p class="mkt-product-card__text mb-5">{{ $product['description'] }}</p>
                        <a href="/contact?product={{ urlencode($product['name']) }}" class="mkt-btn mkt-btn--primary mkt-btn--sm">Request Demo →</a>
                    </div>
                    <div class="lg:col-span-4 p-7 sm:p-8 text-white" style="background:#053171;">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#7DDFFF] mb-2">Main users / market</p>
                        <p class="text-sm leading-relaxed text-white/90">{{ $product['market'] }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

@isset($dbProducts)
    @if($dbProducts->isNotEmpty())
        <section class="mkt-section mkt-section--white">
            <div class="mkt-container">
                <div class="mkt-section__header">
                    <h2 class="mkt-section__title">Additional Listings</h2>
                </div>
                <div class="mkt-products-grid">
                    @foreach($dbProducts as $product)
                        <article class="mkt-product-card">
                            <h3 class="mkt-product-card__title">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="mkt-product-card__text">{{ Str::limit($product->description, 140) }}</p>
                            @endif
                            @if($product->url)
                                <a href="{{ $product->url }}" target="_blank" rel="noopener" class="mkt-product-card__link">Learn more <span aria-hidden="true">→</span></a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endisset

<x-marketing.cta
    title="Interested in a platform?"
    text="Schedule a demo and we’ll walk you through the right Prady product for your market."
/>
@endsection
