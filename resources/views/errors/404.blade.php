@extends('layouts.app')

@section('title', '404 - Page Not Found - Pradytecai')
@section('description', 'The page you are looking for could not be found.')

@section('content')
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden min-h-[80vh] flex items-center">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        
        <div class="section-content w-full mx-auto text-center max-w-2xl">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-indigo-600 mb-4">404</h1>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Page Not Found</h2>
                <p class="text-xl text-gray-600 mb-8">
                    Oops! The page you're looking for doesn't exist or has been moved.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="/" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                    Go to Homepage
                </a>
                <button onclick="window.history.back()" class="bg-white text-indigo-700 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition border-2 border-indigo-600">
                    Go Back
                </button>
            </div>
            
            <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Pages</h3>
                <div class="grid md:grid-cols-3 gap-4 text-sm">
                    <a href="/products" class="text-indigo-600 hover:underline">Products</a>
                    <a href="/services" class="text-indigo-600 hover:underline">Services</a>
                    <a href="/about" class="text-indigo-600 hover:underline">About Us</a>
                    <a href="/blog" class="text-indigo-600 hover:underline">Blog</a>
                    <a href="/careers" class="text-indigo-600 hover:underline">Careers</a>
                    <a href="/contact" class="text-indigo-600 hover:underline">Contact</a>
                </div>
            </div>
        </div>
    </section>
@endsection

