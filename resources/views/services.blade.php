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

<section class="prady-page-hero py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <x-breadcrumbs :items="[
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Industries']
        ]" light="true" />
        <div class="text-center max-w-3xl mx-auto mt-4">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Industries We Serve</h1>
            <p class="text-lg text-white/80">
                Purpose-built platforms for the markets where Prady Technologies is building deep domain software.
            </p>
        </div>
    </div>
</section>

<section class="bg-[#F4F7FB] py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($industries as $item)
                <div class="prady-card">
                    <div class="prady-card__icon">
                        <x-prady-icon :name="$item['icon']" class="w-7 h-7" />
                    </div>
                    <h3 class="text-xl font-bold text-[#0B2347] mb-2">{{ $item['title'] }}</h3>
                    <p class="text-sm text-[#1B3A5F]/75 leading-relaxed mb-4">{{ $item['desc'] }}</p>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#00AEEF]">{{ $item['markets'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-[#0B2347] mb-3">How we deliver</h2>
            <p class="text-[#1B3A5F]/75 max-w-2xl mx-auto">End-to-end services around every Prady platform.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['Custom Development', 'code', 'Bespoke modules and workflows for your institution.'],
                ['Cloud Infrastructure', 'shield', 'Secure hosting, monitoring, and uptime operations.'],
                ['Integrations', 'handshake', 'M-Pesa, payments, GPS devices, and third-party APIs.'],
                ['Training & Support', 'hr', 'Onboarding, documentation, and ongoing support.'],
            ] as $svc)
                <div class="prady-card text-center">
                    <div class="prady-card__icon mx-auto">
                        <x-prady-icon :name="$svc[1]" class="w-7 h-7" />
                    </div>
                    <h3 class="font-bold text-[#0B2347] mb-2">{{ $svc[0] }}</h3>
                    <p class="text-sm text-[#1B3A5F]/70">{{ $svc[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #0B2347, #1A4B8C);">
    <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-extrabold text-white mb-4">Ready to get started?</h2>
        <p class="text-white/80 mb-8">Let’s discuss how a Prady platform can fit your industry.</p>
        <a href="/contact" class="prady-btn-light">Contact Us →</a>
    </div>
</section>
@endsection
