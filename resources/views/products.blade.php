@extends('layouts.app')

@section('title', 'Products — Prady Technologies Ltd')
@section('description', 'Explore the Prady Technologies product portfolio: Microfinance, Rafiki Loan, GPS Tracking, Property Management, SpareMe, Live Commerce, SACCO, Chama, and Mtalii Travel Wallet.')

@section('content')
@php $products = config('portfolio.products'); @endphp

<section class="prady-page-hero py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Products']
        ]" light="true" />
        <div class="text-center max-w-3xl mx-auto mt-4">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Product Portfolio</h1>
            <p class="text-lg text-white/80">
                The immediate Prady Technologies product portfolio — platforms built for finance, mobility, property, commerce, and tourism.
            </p>
        </div>
    </div>
</section>

<section class="bg-[#F4F7FB] py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        @foreach($products as $index => $product)
            <article id="{{ $product['slug'] }}" class="prady-card scroll-mt-28 {{ $index % 2 === 1 ? 'lg:bg-gradient-to-br lg:from-white lg:to-[#E8F6FC]' : '' }}">
                <div class="grid lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-1">
                        <div class="prady-card__icon mb-0">
                            <x-prady-icon :name="$product['icon']" class="w-7 h-7" />
                        </div>
                    </div>
                    <div class="lg:col-span-7">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#00AEEF] mb-2">
                            {{ $index + 1 < 10 ? '0'.($index + 1) : $index + 1 }} · Product
                        </p>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B2347] mb-3">{{ $product['name'] }}</h2>
                        <p class="text-[#1B3A5F]/80 leading-relaxed mb-4">{{ $product['description'] }}</p>
                        <a href="/contact?product={{ urlencode($product['name']) }}" class="prady-btn-primary">Request Demo →</a>
                    </div>
                    <div class="lg:col-span-4">
                        <div class="rounded-2xl bg-[#0B2347] text-white p-6 h-full">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#7DDFFF] mb-2">Main users / market</p>
                            <p class="text-sm leading-relaxed text-white/90">{{ $product['market'] }}</p>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- Optional CMS products if present --}}
@isset($dbProducts)
    @if($dbProducts->isNotEmpty())
        <section class="bg-white py-16 px-4 sm:px-6 lg:px-8 border-t border-slate-100">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-3xl font-extrabold text-[#0B2347] mb-8 text-center">Additional Listings</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($dbProducts as $product)
                        <div class="prady-card">
                            <h3 class="text-xl font-bold text-[#0B2347] mb-2">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="text-sm text-[#1B3A5F]/75 mb-4">{{ Str::limit($product->description, 140) }}</p>
                            @endif
                            @if($product->url)
                                <a href="{{ $product->url }}" target="_blank" class="text-[#00AEEF] font-semibold text-sm">Learn more →</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endisset

<section class="py-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #0B2347, #1A4B8C);">
    <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-extrabold text-white mb-4">Interested in a platform?</h2>
        <p class="text-white/80 mb-8">Schedule a demo and we’ll walk you through the right Prady product for your market.</p>
        <a href="/contact" class="prady-btn-light">Get Demo →</a>
    </div>
</section>
@endsection
