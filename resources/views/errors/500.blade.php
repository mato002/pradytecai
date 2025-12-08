@extends('layouts.app')

@section('title', '500 - Server Error - Pradytecai')
@section('description', 'An error occurred on our server.')

@section('content')
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden min-h-[80vh] flex items-center">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        
        <div class="section-content w-full mx-auto text-center max-w-2xl">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-red-600 mb-4">500</h1>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Server Error</h2>
                <p class="text-xl text-gray-600 mb-8">
                    We're experiencing some technical difficulties. Our team has been notified and is working on a fix.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="/" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                    Go to Homepage
                </a>
                <a href="/contact" class="bg-white text-indigo-700 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition border-2 border-indigo-600">
                    Contact Support
                </a>
            </div>
            
            <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                <p class="text-sm text-gray-600">
                    If this problem persists, please <a href="/contact" class="text-indigo-600 hover:underline">contact our support team</a> and we'll help you right away.
                </p>
            </div>
        </div>
    </section>
@endsection

