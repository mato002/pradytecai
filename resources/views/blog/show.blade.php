@extends('layouts.app')

@section('title', $post->title . ' - Pradytecai Blog')
@section('description', $post->excerpt ?? 'Read the latest from Pradytecai')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Background image --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1920&q=80');"></div>
        {{-- Overlay for readability --}}
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/85 via-purple-900/80 to-pink-900/85"></div>
        
        <div class="relative z-10 w-full mx-auto max-w-4xl">
            <nav class="mb-6 text-sm">
                <ol class="flex items-center space-x-2 text-slate-300">
                    <li><a href="/" class="hover:text-white transition">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-white">{{ Str::limit($post->title, 50) }}</li>
                </ol>
            </nav>
            
            <div class="text-center">
                @if($post->category)
                    <span class="inline-block px-4 py-2 rounded-full bg-indigo-600/30 text-indigo-200 text-sm font-semibold mb-4">
                        {{ $post->category }}
                    </span>
                @endif
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $post->title }}</h1>
                <div class="flex items-center justify-center space-x-4 text-slate-300 text-sm">
                    <span>{{ optional($post->published_at ?? $post->created_at)->format('F j, Y') }}</span>
                    <span>•</span>
                    <span>{{ ceil(str_word_count(strip_tags($post->body)) / 200) }} min read</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Content -->
    <article class="relative py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1920&q=80');"></div>
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        
        <div class="section-content w-full mx-auto max-w-4xl">
            <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
                <div class="prose prose-lg max-w-none">
                    {!! nl2br(e($post->body)) !!}
                </div>
                
                <!-- Social Sharing -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
                    <div class="flex items-center space-x-4">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" 
                           target="_blank" 
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-sky-100 text-sky-600 hover:bg-sky-200 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 4h3.039l-6.64 7.59L22 20h-3.18l-3.92-4.6-3.52 4.6H8.34l6.64-7.59L6 4h3.18l3.42 4.01L16.48 4z" />
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                           target="_blank" 
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.98 3.5C3.88 3.5 3 4.38 3 5.47c0 1.06.86 1.93 1.96 1.93h.02c1.1 0 1.98-.87 1.98-1.93C6.96 4.38 6.08 3.5 4.98 3.5zM3.25 8.75h3.47V20.5H3.25V8.75zM9.5 8.75h3.32v1.6h.05c.46-.87 1.57-1.78 3.23-1.78 3.45 0 4.09 2.27 4.09 5.22v6.71h-3.47v-5.95c0-1.42-.03-3.24-1.98-3.24-1.98 0-2.29 1.54-2.29 3.14v6.05H9.5V8.75z" />
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                           target="_blank" 
                           class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10h2.5l-.3 3H13v7h-3v-7H8v-3h2V8.5C10 6.57 11.57 5 13.5 5H17v3h-2c-.55 0-1 .45-1 1V10z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Related Posts -->
            @if($relatedPosts->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Related Articles</h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($relatedPosts as $relatedPost)
                            <article class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-shadow">
                                <div class="h-48 bg-gradient-to-br from-indigo-100 to-sky-200"></div>
                                <div class="p-6">
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <span>{{ optional($relatedPost->published_at ?? $relatedPost->created_at)->format('F j, Y') }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">
                                        <a href="{{ route('blog.show', $relatedPost) }}" class="hover:text-indigo-600 transition">
                                            {{ $relatedPost->title }}
                                        </a>
                                    </h3>
                                    @if($relatedPost->excerpt)
                                        <p class="text-gray-600 mb-4 text-sm">
                                            {{ Str::limit($relatedPost->excerpt, 100) }}
                                        </p>
                                    @endif
                                    <a href="{{ route('blog.show', $relatedPost) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                                        Read more →
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Back to Blog -->
            <div class="mt-12 text-center">
                <a href="{{ route('blog.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Blog
                </a>
            </div>
        </div>
    </article>
@endsection

