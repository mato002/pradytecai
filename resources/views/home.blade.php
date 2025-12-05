@extends('layouts.app')

@section('title', 'Pradytecai - Enterprise Software Solutions')
@section('description', 'Pradytecai offers cutting-edge software solutions including BulkSMS CRM, Microfinance Management, and more. Transform your business with our innovative platforms.')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900/80 via-indigo-900/70 to-slate-900/80"></div>
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0VjIySDI0djEySDEyVjM0aDEyVjQ2aDEyVjM0em0wLTEyVjEwSDI0djEySDEyVjIySDBWMTBoMTJWMEgyNHYxMHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        <div class="relative z-10 w-full mx-auto max-w-6xl text-center hero-animate">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                <span class="hero-word">Enterprise</span>
                <span class="hero-word">Software</span>
                <span class="hero-word">Solutions</span>
                <br class="md:hidden">
                <span class="text-indigo-600">
                    <span class="hero-word">That</span>
                    <span class="hero-word">Drive</span>
                    <span class="hero-word">Success</span>
                </span>
            </h1>
            <p class="hero-description text-xl text-slate-200 mb-8 max-w-3xl mx-auto">
                Transform your business with our comprehensive suite of software platforms. 
                From multi-channel messaging to microfinance management, we've got you covered.
            </p>
            <div class="hero-buttons flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/products" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                    Explore Solutions
                </a>
                <a href="/contact" class="bg-white text-indigo-700 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition border-2 border-indigo-600">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section id="solutions" class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        <div class="section-content w-full mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Solutions</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Discover our powerful software platforms designed to streamline your operations
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- BulkSMS CRM -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">BulkSMS CRM</h3>
                    <p class="text-gray-600 mb-6">
                        Multi-channel messaging platform for SMS, WhatsApp, and Email. Manage contacts, create campaigns, 
                        and track delivery with comprehensive analytics.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Multi-channel messaging
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Contact management
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Real-time analytics
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            API integration
                        </li>
                    </ul>
                    <a href="https://crm.pradytecai.com" target="_blank" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Open BulkSMS CRM →
                    </a>
                </div>

                <!-- Microfinance System -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Prady Mfi</h3>
                    <p class="text-gray-600 mb-6">
                        Comprehensive microfinance management system for loan processing, client management, 
                        and financial reporting. Streamline your MFI operations.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Loan management
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Client database
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Financial reporting
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Payment tracking
                        </li>
                    </ul>
                    <a href="https://demo.pradytecai.com" target="_blank" class="block w-full bg-green-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                        View Demo →
                    </a>
                </div>

                <!-- Additional Platform -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Enterprise Solutions</h3>
                    <p class="text-gray-600 mb-6">
                        Custom enterprise software solutions tailored to your business needs. 
                        Scalable, secure, and built for growth.
                    </p>
                    <ul class="space-y-2 mb-6 text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Custom development
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Cloud infrastructure
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Security & compliance
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            24/7 support
                        </li>
                    </ul>
                    <a href="/products" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Learn More →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-slate"></div>
        <div class="section-content w-full mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose Pradytecai?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We deliver reliable, scalable solutions that grow with your business
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lightning Fast</h3>
                    <p class="text-gray-600">Optimized performance and quick setup. Get started in minutes, not days.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Secure & Reliable</h3>
                    <p class="text-gray-600">Enterprise-grade security with 99.9% uptime guarantee. Your data is safe with us.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Dedicated support team available around the clock to help you succeed.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-gradient"></div>
        <div class="section-content w-full mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
            <p class="text-xl text-gray-600 mb-8">
                Join hundreds of businesses already using our platforms to transform their operations.
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