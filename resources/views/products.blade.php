@extends('layouts.app')

@section('title', 'Products — Prady Technologies Ltd')
@section('description', 'Explore the Prady Technologies product portfolio: Microfinance, Rafiki Loan, GPS Tracking, Property Management, SpareMe, Live Commerce, SACCO, Chama, and Mtalii Travel Wallet.')

@section('content')
@php
    use Illuminate\Support\Str;
    $catalog = $products ?? collect();
@endphp

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
        @foreach($catalog as $index => $product)
            @php
                $slug = is_array($product) ? ($product['slug'] ?? Str::slug($product['name'])) : ($product->slug ?? Str::slug($product->name));
                $name = is_array($product) ? $product['name'] : $product->name;
                $description = is_array($product) ? ($product['description'] ?? $product['short'] ?? '') : ($product->description ?? $product->short ?? '');
                $market = is_array($product) ? ($product['market'] ?? '') : ($product->market ?? '');
                $icon = is_array($product) ? ($product['icon'] ?? 'cog') : ($product->icon ?? 'cog');
            @endphp
            <article id="{{ $slug }}" class="mkt-product-card scroll-mt-28 !flex-row !items-stretch !p-0 overflow-hidden">
                <div class="grid lg:grid-cols-12 gap-0 w-full">
                    <div class="lg:col-span-8 p-7 sm:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[var(--prady-sky)] mb-2">
                            {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} · Product
                        </p>
                        <div class="flex items-start gap-4 mb-3">
                            <span class="mkt-product-card__icon !mb-0 shrink-0" aria-hidden="true">
                                <x-prady-icon :name="$icon" class="w-7 h-7" />
                            </span>
                            <h2 class="mkt-product-card__title !mb-0 text-2xl sm:text-[1.7rem]">{{ $name }}</h2>
                        </div>
                        <p class="mkt-product-card__text mb-5">{{ $description }}</p>
                        <a href="/contact?product={{ urlencode($slug) }}" class="mkt-btn mkt-btn--primary mkt-btn--sm">Request Demo →</a>
                    </div>
                    <div class="lg:col-span-4 p-7 sm:p-8 text-white" style="background:#053171;">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#7DDFFF] mb-2">Main users / market</p>
                        <p class="text-sm leading-relaxed text-white/90">{{ $market }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<x-marketing.cta
    title="Interested in a platform?"
    text="Schedule a demo and we’ll walk you through the right Prady product for your market."
/>
@endsection
