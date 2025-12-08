@extends('layouts.app')

@section('title', 'Blog & Updates - Pradytecai')
@section('description', 'Stay updated with the latest news, articles, and updates from Pradytecai.')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-purple-900/80 to-pink-900/85"></div>
        {{-- Subtle pattern overlay --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0VjIySDI0djEySDEyVjM0aDEyVjQ2aDEyVjM0em0wLTEyVjEwSDI0djEySDEyVjIySDBWMTBoMTJWMEgyNHYxMHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        <div class="relative z-10 w-full mx-auto max-w-6xl hero-animate">
            <x-breadcrumbs :items="[
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Blog']
            ]" light="true" />
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">Blog & Updates</h1>
                <p class="text-xl text-slate-200">
                    Stay informed with the latest news, insights, and updates from Pradytecai
                </p>
            </div>
        </div>
    </section>

    <!-- Blog Posts -->
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        <div class="section-content w-full mx-auto">
            @if($posts->isEmpty())
                <div class="max-w-xl mx-auto text-center">
                    <p class="text-lg text-gray-600 mb-4">
                        No articles published yet. Check back soon for updates from the Pradytecai team.
                    </p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <article class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="h-48 bg-gradient-to-br from-indigo-100 to-sky-200" loading="lazy"></div>
                            <div class="p-6">
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <span>{{ optional($post->published_at ?? $post->created_at)->format('F j, Y') }}</span>
                                    @if($post->category)
                                        <span class="mx-2">•</span>
                                        <span>{{ $post->category }}</span>
                                    @endif
                                </div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                                    <a href="{{ route('blog.show', $post) }}" class="hover:text-indigo-600 transition">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                @if($post->excerpt)
                                    <p class="text-gray-600 mb-4">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                                <a href="{{ route('blog.show', $post) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                                    Read more
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <!-- Newsletter Signup -->
            <div class="mt-16 bg-gradient-to-r from-sky-50 via-white to-indigo-50 rounded-xl p-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Stay Updated</h2>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                    Subscribe to our newsletter to receive the latest updates, product announcements, 
                    and industry insights directly in your inbox.
                </p>
                @if(session('newsletter_success'))
                    <div class="max-w-md mx-auto mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-700 text-sm">
                        {{ session('newsletter_success') }}
                    </div>
                @endif
                <form id="newsletter-form" action="/newsletter/subscribe" method="POST" class="max-w-md mx-auto flex gap-4">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" required class="flex-1 px-4 py-3 rounded-lg text-gray-900 border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
