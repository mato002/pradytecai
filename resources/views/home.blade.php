@extends('layouts.app')

@section('title', 'Prady Technologies Ltd — Smart Technology Solutions')
@section('description', 'Prady Technologies Ltd builds secure, efficient software that drives growth. Microfinance, SACCO, GPS, property, SpareMe, and more.')

@section('content')
@php
    $products = config('portfolio.products');
    $featured = collect($products)->where('featured', true)->values();
@endphp

{{-- Hero --}}
<section class="prady-hero">
    <div class="prady-hero__glow" style="top: -80px; right: 8%;"></div>
    <div class="prady-hero__glow" style="bottom: -120px; left: -60px; animation-delay: -3s;"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="hero-animate">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#7DDFFF] mb-5">
                    Doing It Differently
                </p>
                <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-extrabold text-white leading-[1.12] tracking-tight mb-5">
                    Smart Technology Solutions for Ambitious Businesses
                </h1>
                <p class="text-lg text-white/80 max-w-xl mb-8 leading-relaxed">
                    We build secure, efficient software that drives growth.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/contact" class="prady-btn-light">Get Demo →</a>
                    <a href="/products" class="prady-btn-ghost">Our Products</a>
                </div>
            </div>

            <div class="hero-animate delay-md relative flex justify-center lg:justify-end">
                <div class="prady-illustration w-full max-w-md">
                    {{-- Laptop mock --}}
                    <div class="relative mx-auto">
                        <div class="rounded-2xl bg-white/10 border border-white/20 p-3 backdrop-blur-sm shadow-2xl">
                            <div class="rounded-xl bg-white overflow-hidden">
                                <div class="h-8 bg-[#0B2347] flex items-center gap-1.5 px-3">
                                    <span class="w-2 h-2 rounded-full bg-[#00AEEF]"></span>
                                    <span class="w-2 h-2 rounded-full bg-white/40"></span>
                                    <span class="w-2 h-2 rounded-full bg-white/25"></span>
                                </div>
                                <div class="p-4 grid grid-cols-3 gap-3 bg-gradient-to-br from-[#F4F7FB] to-[#E8F6FC]">
                                    <div class="col-span-2 rounded-lg bg-white p-3 shadow-sm border border-slate-100">
                                        <div class="h-2 w-16 bg-[#0B2347]/20 rounded mb-3"></div>
                                        <div class="flex items-end gap-1.5 h-20">
                                            <div class="flex-1 bg-[#00AEEF]/30 rounded-t" style="height:40%"></div>
                                            <div class="flex-1 bg-[#00AEEF]/55 rounded-t" style="height:65%"></div>
                                            <div class="flex-1 bg-[#1A4B8C] rounded-t" style="height:85%"></div>
                                            <div class="flex-1 bg-[#00AEEF]/70 rounded-t" style="height:55%"></div>
                                            <div class="flex-1 bg-[#0B2347] rounded-t" style="height:95%"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="rounded-lg bg-white p-3 shadow-sm border border-slate-100">
                                            <div class="h-2 w-10 bg-[#0B2347]/20 rounded mb-2"></div>
                                            <div class="text-lg font-extrabold text-[#0B2347]">98%</div>
                                        </div>
                                        <div class="rounded-lg bg-white p-3 shadow-sm border border-slate-100">
                                            <div class="h-2 w-8 bg-[#0B2347]/20 rounded mb-2"></div>
                                            <div class="h-8 w-8 rounded-full border-4 border-[#00AEEF] border-r-transparent"></div>
                                        </div>
                                    </div>
                                    <div class="col-span-3 rounded-lg bg-white p-3 shadow-sm border border-slate-100 flex gap-2">
                                        <div class="h-2 flex-1 bg-[#E8F6FC] rounded"></div>
                                        <div class="h-2 flex-1 bg-[#00AEEF]/40 rounded"></div>
                                        <div class="h-2 flex-1 bg-[#0B2347]/15 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mx-auto h-3 w-[72%] rounded-b-xl bg-[#07182F]"></div>
                        <div class="mx-auto h-1.5 w-[40%] rounded-b bg-[#0B2347]"></div>
                    </div>

                    <div class="prady-illustration__float" style="top: 8%; left: -4%; animation-delay: -1s;">
                        <x-prady-icon name="shield" class="w-5 h-5" />
                    </div>
                    <div class="prady-illustration__float" style="top: 18%; right: -2%; animation-delay: -2.5s;">
                        <x-prady-icon name="users" class="w-5 h-5" />
                    </div>
                    <div class="prady-illustration__float" style="bottom: 22%; left: -6%; animation-delay: -4s;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <div class="prady-illustration__float" style="bottom: 12%; right: 0; animation-delay: -0.8s;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Solutions --}}
<section id="solutions" class="bg-[#F4F7FB] py-20 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="text-center mb-14 hero-animate">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#0B2347] mb-3">Our Solutions</h2>
            <p class="text-lg text-[#1B3A5F]/75 max-w-2xl mx-auto">
                Tailored systems designed to streamline operations and scale your business.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @foreach($featured as $product)
                <a href="/products#{{ $product['slug'] }}" class="prady-card block group">
                    <div class="prady-card__icon">
                        <x-prady-icon :name="$product['icon']" class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-bold text-[#0B2347] mb-2 group-hover:text-[#1A4B8C] transition">{{ $product['name'] }}</h3>
                    <p class="text-sm text-[#1B3A5F]/70 leading-relaxed">{{ $product['short'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(collect($products)->where('featured', false) as $product)
                <a href="/products#{{ $product['slug'] }}" class="prady-card block group">
                    <div class="prady-card__icon">
                        <x-prady-icon :name="$product['icon']" class="w-7 h-7" />
                    </div>
                    <h3 class="text-lg font-bold text-[#0B2347] mb-2 group-hover:text-[#1A4B8C] transition">{{ $product['name'] }}</h3>
                    <p class="text-sm text-[#1B3A5F]/70 leading-relaxed mb-3">{{ $product['short'] }}</p>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#00AEEF]">{{ $product['market'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Trust bar --}}
<section class="prady-trust py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid sm:grid-cols-3 gap-6 text-center sm:text-left">
            <div class="flex items-center justify-center sm:justify-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-[#0B2347] shadow-sm">
                    <x-prady-icon name="shield" class="w-5 h-5" />
                </div>
                <div>
                    <p class="font-bold text-[#0B2347]">ISO 27001 Ready</p>
                    <p class="text-xs text-[#1B3A5F]/65">Security-first platforms</p>
                </div>
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-[#0B2347] shadow-sm">
                    <x-prady-icon name="clock" class="w-5 h-5" />
                </div>
                <div>
                    <p class="font-bold text-[#0B2347]">99.9% Uptime</p>
                    <p class="text-xs text-[#1B3A5F]/65">Reliable cloud operations</p>
                </div>
            </div>
            <div class="flex items-center justify-center sm:justify-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-[#0B2347] shadow-sm">
                    <x-prady-icon name="building" class="w-5 h-5" />
                </div>
                <div>
                    <p class="font-bold text-[#0B2347]">Built for Growth</p>
                    <p class="text-xs text-[#1B3A5F]/65">Trusted by ambitious teams</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden" style="background: linear-gradient(135deg, #0B2347 0%, #1A4B8C 55%, #00AEEF 160%);">
    <div class="relative z-10 mx-auto max-w-3xl text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Ready to do it differently?</h2>
        <p class="text-lg text-white/80 mb-8">
            Tell us about your institution or product idea — we'll show you the right Prady platform.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/contact" class="prady-btn-light">Get Demo →</a>
            <a href="/products" class="prady-btn-ghost">Browse Products</a>
        </div>
    </div>
</section>
@endsection
