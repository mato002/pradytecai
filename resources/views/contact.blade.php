@extends('layouts.app')

@section('title', 'Contact Us - Pradytecai')
@section('description', 'Get in touch with Pradytecai. We are here to help you find the right solution for your business needs.')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-sky-900/85 via-indigo-900/80 to-teal-900/85"></div>
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0VjIySDI0djEySDEyVjM0aDEyVjQ2aDEyVjM0em0wLTEyVjEwSDI0djEySDEyVjIySDBWMTBoMTJWMEgyNHYxMHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        <div class="relative z-10 w-full mx-auto max-w-6xl hero-animate">
            <x-breadcrumbs :items="[
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Contact']
            ]" light="true" />
            <div class="text-center">
                <p class="inline-flex items-center px-3 py-1 rounded-full bg-white/90 border border-indigo-200 text-xs font-medium text-indigo-700 mb-4 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Typically replies in under 2 minutes
                </p>
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-4">Let's Talk About Your Next Project</h1>
            <p class="text-xl text-slate-200 mb-6">
                Whether you're exploring BulkSMS CRM, Prady Mfi, or a custom enterprise solution, our team is ready to help.
            </p>
                <p class="text-sm text-slate-300">
                    Prefer email or phone? Use the details below and we'll make sure you reach the right person.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1551434678-e076c223a692?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        <div class="section-content w-full mx-auto grid lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] gap-10 lg:gap-16 items-start">
            <!-- Form Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Tell Us How We Can Help</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Share a few details and we’ll connect you with the right specialist from our team.
                </p>

                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Full Name *</label>
                            <input type="text" id="name" name="name" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="company" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Company</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Work Email *</label>
                            <input type="email" id="email" name="email" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Phone</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="topic" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">What are you interested in? *</label>
                        <select id="topic" name="topic" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Select an option</option>
                            <option value="bulksms">BulkSMS CRM</option>
                            <option value="mfi">Prady Mfi</option>
                            <option value="custom">Custom enterprise solution</option>
                            <option value="support">Technical support</option>
                            <option value="partnership">Partnerships</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Subject *</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            value="{{ request('position') ? 'Application: ' . request('position') : '' }}"
                        >
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">How can we help? *</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Share a bit about your use case, timelines, and anything else that’s helpful."
                        ></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                            Submit Request
                        </button>
                        <p class="text-[11px] text-gray-500 max-w-xs">
                            By submitting, you agree that we may contact you about Pradytecai products and services.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Info / Sidebar -->
            <div class="space-y-6">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Direct contact</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Email</p>
                                <a href="mailto:mathiasodhis@gmail.com" class="text-sm text-indigo-600 font-medium hover:underline">mathiasodhis@gmail.com</a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-full bg-sky-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Phone</p>
                                <a href="tel:+254728883160" class="text-sm text-indigo-600 font-medium hover:underline">+254 728 883 160</a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 111.414-1.414L5.636 9.172A8.003 8.003 0 0119 12a7.963 7.963 0 01-1.343 4.414z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Location</p>
                                <p class="text-sm text-gray-700">Nairobi, Kenya</p>
                                <p class="text-[11px] text-gray-500 mt-1">Serving customers across Kenya and beyond.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Business Hours</p>
                                <p class="text-sm text-gray-700">Mon – Fri: 8:00 AM – 6:00 PM EAT</p>
                                <p class="text-[11px] text-gray-500 mt-1">For urgent issues, open a ticket via your CRM dashboard.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://crm.pradytecai.com" target="_blank" class="text-indigo-700 hover:underline">BulkSMS CRM platform</a></li>
                        <li><a href="https://demo.pradytec.com" target="_blank" class="text-indigo-700 hover:underline">Prady Mfi live demo</a></li>
                        <li><a href="/products" class="text-indigo-700 hover:underline">All products & solutions</a></li>
                        <li><a href="/services" class="text-indigo-700 hover:underline">Implementation & consulting</a></li>
                        <li><a href="/policies" class="text-indigo-700 hover:underline">Terms, privacy & compliance</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
