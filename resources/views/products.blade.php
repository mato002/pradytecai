@extends('layouts.app')

@section('title', 'Products & Solutions - Pradytecai')
@section('description', 'Explore our comprehensive suite of software products including BulkSMS CRM, Microfinance Management, and enterprise solutions.')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-blue-900/80 to-sky-900/85"></div>
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0VjIySDI0djEySDEyVjM0aDEyVjQ2aDEyVjM0em0wLTEyVjEwSDI0djEySDEyVjIySDBWMTBoMTJWMEgyNHYxMHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        <div class="relative z-10 w-full mx-auto max-w-6xl hero-animate">
            <x-breadcrumbs :items="[
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Products']
            ]" light="true" />
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">Products & Solutions</h1>
                <p class="text-xl text-slate-200">
                    Powerful software platforms designed to transform your business operations
                </p>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        <div class="section-content w-full mx-auto">
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">No Products Available</h2>
                    <p class="text-lg text-gray-600">Check back soon for our latest products and solutions.</p>
                </div>
            @else
                @php
                    // Separate products by type - main products vs enterprise solutions
                    $mainProducts = $products->filter(function($product) {
                        return !$product->type || strtolower($product->type) !== 'enterprise';
                    });
                    $enterpriseProducts = $products->filter(function($product) {
                        return $product->type && strtolower($product->type) === 'enterprise';
                    });
                @endphp

                {{-- Main Products (Featured) --}}
                @if($mainProducts->isNotEmpty())
                    @foreach($mainProducts as $index => $product)
                        <div class="mb-20 {{ $index > 0 ? 'mt-20' : '' }}">
                            <div class="grid md:grid-cols-2 gap-12 items-center">
                                <div class="{{ $index % 2 == 1 ? 'md:order-2' : '' }}">
                                    <div class="inline-block w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                                        @if($product->icon === 'messaging' || (!$product->icon && str_contains(strtolower($product->name), 'sms')))
                                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        @elseif($product->icon === 'finance' || (!$product->icon && str_contains(strtolower($product->name), 'mfi')))
                                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <h2 class="text-4xl font-bold text-gray-900 mb-2">{{ $product->name }}</h2>
                                    @if($product->type)
                                        <p class="text-sm font-medium text-indigo-600 mb-4">
                                            {{ $product->type }}
                                        </p>
                                    @else
                                        <div class="mb-2"></div>
                                    @endif
                                    @if($product->description)
                                        <p class="text-lg text-gray-600 mb-6 whitespace-pre-line">{{ $product->description }}</p>
                                    @endif
                                    
                                    @if($product->features || $product->benefits)
                                        <div class="grid grid-cols-2 gap-4 mb-6">
                                            @if($product->features && count($product->features) > 0)
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 mb-2">Key Features</h4>
                                                    <ul class="space-y-2 text-gray-600">
                                                        @foreach($product->features as $feature)
                                                            <li class="flex items-center">
                                                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                {{ $feature }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            @if($product->benefits && count($product->benefits) > 0)
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 mb-2">Benefits</h4>
                                                    <ul class="space-y-2 text-gray-600">
                                                        @foreach($product->benefits as $benefit)
                                                            <li class="flex items-center">
                                                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                {{ $benefit }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($product->url)
                                        <a href="{{ $product->url }}" target="_blank" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                            {{ $product->button_text ?? 'Learn More' }} →
                                        </a>
                                    @endif
                                </div>
                                <div class="{{ $index % 2 == 1 ? 'md:order-1' : '' }} bg-gradient-to-br from-indigo-50 to-sky-100 rounded-xl p-8">
                                    <div class="text-center">
                                        @if($product->statistics && count($product->statistics) > 0)
                                            @php
                                                $stats = array_values($product->statistics);
                                                $statCount = count($stats);
                                            @endphp
                                            @for($i = 0; $i < $statCount; $i += 2)
                                                @if(isset($stats[$i]) && isset($stats[$i + 1]))
                                                    <div class="{{ $i > 0 ? 'mt-6' : '' }}">
                                                        <div class="text-5xl font-bold text-indigo-600 mb-2">{{ $stats[$i] }}</div>
                                                        <div class="text-gray-600 mb-6">{{ $stats[$i + 1] }}</div>
                                                    </div>
                                                @endif
                                            @endfor
                                        @else
                                            @if($product->type)
                                                <div class="text-2xl font-bold text-indigo-600 mb-2">{{ $product->type }}</div>
                                                <div class="text-gray-600 mb-6">Product Type</div>
                                            @endif
                                            <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $product->name }}</div>
                                            <div class="text-gray-600">Available Now</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Enterprise Solutions (Grid) --}}
                @if($enterpriseProducts->isNotEmpty())
                    <div class="mt-20">
                        <div class="text-center mb-12">
                            <h2 class="text-4xl font-bold text-gray-900 mb-4">Enterprise Solutions</h2>
                            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                                Custom software solutions tailored to your specific business needs
                            </p>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8">
                            @foreach($enterpriseProducts as $product)
                                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                                    <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                                    @if($product->type)
                                        <p class="text-xs font-medium text-indigo-600 mb-3">{{ $product->type }}</p>
                                    @endif
                                    @if($product->description)
                                        <p class="text-gray-600 mb-4">{{ Str::limit($product->description, 150) }}</p>
                                    @endif
                                    @if($product->url)
                                        <a href="{{ $product->url }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                                            Learn More →
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- If no type separation, show all products in a grid --}}
                @if($mainProducts->isEmpty() && $enterpriseProducts->isEmpty())
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($products as $product)
                            <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-lg transition-shadow">
                                <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h3>
                                @if($product->type)
                                    <p class="text-sm text-indigo-600 font-medium mb-3">{{ $product->type }}</p>
                                @endif
                                @if($product->description)
                                    <p class="text-gray-600 mb-4">{{ Str::limit($product->description, 150) }}</p>
                                @endif
                                @if($product->url)
                                    <a href="{{ $product->url }}" target="_blank" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition text-sm">
                                        Learn More →
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-gradient"></div>
        <div class="section-content w-full mx-auto text-center hero-animate delay-md">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Interested in Our Products?</h2>
            <p class="text-xl text-gray-600 mb-8">
                Get in touch to learn more or schedule a demo.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/contact" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                    Contact Us
                </a>
                <a href="https://crm.pradytecai.com" target="_blank" class="bg-white text-indigo-700 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition border-2 border-indigo-600">
                    Open CRM
                </a>
            </div>
        </div>
    </section>
@endsection
