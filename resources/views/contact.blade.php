@extends('layouts.app')

@section('title', 'Contact Us — Prady Technologies Ltd')
@section('description', 'Get in touch with Prady Technologies Ltd. We are here to help you find the right solution for your business needs.')

@section('content')
@php $contact = config('portfolio.contact', []); @endphp

<x-marketing.page-hero
    title="Let's Talk About Your Next Project"
    subtitle="Whether you're exploring a Prady platform or a custom solution, our team is ready to help."
    :breadcrumbs="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Contact'],
    ]"
/>

<section class="mkt-section mkt-section--light">
    <div class="mkt-container grid lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] gap-8 lg:gap-12 items-start">
        <div class="mkt-product-card !p-6 sm:!p-8">
            <h2 class="mkt-product-card__title text-2xl">Tell Us How We Can Help</h2>
            <p class="mkt-product-card__text mb-6">Share a few details and we’ll connect you with the right specialist.</p>

            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="mkt-label">Full Name *</label>
                        <input type="text" id="name" name="name" required class="mkt-input">
                    </div>
                    <div>
                        <label for="company" class="mkt-label">Company</label>
                        <input type="text" id="company" name="company" class="mkt-input">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="mkt-label">Work Email *</label>
                        <input type="email" id="email" name="email" required class="mkt-input">
                    </div>
                    <div>
                        <label for="phone" class="mkt-label">Phone</label>
                        <input type="tel" id="phone" name="phone" class="mkt-input">
                    </div>
                </div>

                <div>
                    <label for="topic" class="mkt-label">What are you interested in? *</label>
                    <select id="topic" name="topic" required class="mkt-select">
                        <option value="">Select an option</option>
                        <option value="microfinance">Prady Microfinance</option>
                        <option value="sacco">SACCO System</option>
                        <option value="gps">GPS Hosting & Tracking</option>
                        <option value="property">Property Management</option>
                        <option value="custom">Custom enterprise solution</option>
                        <option value="support">Technical support</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="subject" class="mkt-label">Subject *</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        required
                        class="mkt-input"
                        value="{{ request('position') ? 'Application: ' . request('position') : (request('product') ? 'Demo: ' . request('product') : '') }}"
                    >
                </div>

                <div>
                    <label for="message" class="mkt-label">How can we help? *</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        required
                        class="mkt-textarea"
                        placeholder="Share a bit about your use case, timelines, and anything else that’s helpful."
                    ></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                    <button type="submit" class="mkt-btn mkt-btn--primary">Submit Request</button>
                    <p class="text-[11px] text-[var(--text-secondary)] max-w-xs">
                        By submitting, you agree that we may contact you about Prady Technologies products and services.
                    </p>
                </div>
            </form>
        </div>

        <div class="space-y-5">
            <div class="mkt-product-card">
                <h3 class="mkt-product-card__title text-lg">Direct contact</h3>
                <div class="space-y-4 mt-4">
                    @foreach(($contact['emails'] ?? [['label' => 'Email', 'email' => $contact['email'] ?? 'marketing@pradytecai.com']]) as $item)
                        <div class="flex items-start gap-3">
                            <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="chat" class="w-5 h-5" /></span>
                            <div>
                                <p class="mkt-label !mb-1">{{ $item['label'] }}</p>
                                <a href="mailto:{{ $item['email'] }}" class="text-[var(--prady-blue)] font-medium hover:underline">
                                    {{ $item['email'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex items-start gap-3">
                        <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="support" class="w-5 h-5" /></span>
                        <div>
                            <p class="mkt-label !mb-1">Phone</p>
                            <a href="{{ $contact['phone_href'] ?? 'tel:+254722295194' }}" class="text-[var(--prady-blue)] font-medium hover:underline">
                                {{ $contact['phone'] ?? '+254 722 295 194' }}
                            </a>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="location" class="w-5 h-5" /></span>
                        <div>
                            <p class="mkt-label !mb-1">Location</p>
                            <p class="text-[var(--text-primary)] text-sm">{{ $contact['location'] ?? 'Nairobi, Kenya' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mkt-about__point-icon" aria-hidden="true"><x-prady-icon name="clock" class="w-5 h-5" /></span>
                        <div>
                            <p class="mkt-label !mb-1">Business Hours</p>
                            <p class="text-[var(--text-primary)] text-sm">{{ $contact['hours'] ?? 'Mon – Fri: 8:00 AM – 6:00 PM EAT' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mkt-product-card">
                <h3 class="mkt-product-card__title text-lg">Quick links</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="/products" class="text-[var(--prady-blue)] hover:underline">All products &amp; solutions</a></li>
                    <li><a href="/services" class="text-[var(--prady-blue)] hover:underline">Industries we serve</a></li>
                    <li><a href="/policies" class="text-[var(--prady-blue)] hover:underline">Terms, privacy &amp; compliance</a></li>
                    <li><a href="https://crm.pradytecai.com" target="_blank" rel="noopener" class="text-[var(--prady-blue)] hover:underline">BulkSMS CRM platform</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
