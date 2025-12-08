@extends('layouts.app')

@section('title', 'Search Results - Pradytecai')
@section('description', 'Search results for your query')

@section('content')
    <section class="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="section-bg-image" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&q=80');"></div>
        <div class="section-bg-overlay section-bg-overlay-light"></div>
        
        <div class="section-content w-full mx-auto max-w-6xl">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Search Results</h1>
                <form action="{{ route('search') }}" method="GET" class="max-w-2xl">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Search for articles, jobs, and more..." 
                            value="{{ $query }}"
                            class="w-full px-6 py-4 border-2 border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            
            @if($query)
                @if(empty($results['blog_posts']) && empty($results['positions']))
                    <div class="bg-white rounded-xl p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">No results found</h2>
                        <p class="text-gray-600">Try different keywords or browse our <a href="/blog" class="text-indigo-600 hover:underline">blog</a> and <a href="/careers" class="text-indigo-600 hover:underline">careers</a> pages.</p>
                    </div>
                @else
                    @if(!empty($results['blog_posts']) && $results['blog_posts']->isNotEmpty())
                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Blog Posts ({{ $results['blog_posts']->count() }})</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($results['blog_posts'] as $post)
                                    <article class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                                        <div class="flex items-center text-sm text-gray-500 mb-3">
                                            <span>{{ optional($post->published_at ?? $post->created_at)->format('F j, Y') }}</span>
                                            @if($post->category)
                                                <span class="mx-2">•</span>
                                                <span>{{ $post->category }}</span>
                                            @endif
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                                            <a href="{{ route('blog.show', $post) }}" class="hover:text-indigo-600 transition">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        @if($post->excerpt)
                                            <p class="text-gray-600 mb-4">
                                                {{ Str::limit($post->excerpt, 150) }}
                                            </p>
                                        @endif
                                        <a href="{{ route('blog.show', $post) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                                            Read more →
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(!empty($results['positions']) && $results['positions']->isNotEmpty())
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Job Positions ({{ $results['positions']->count() }})</h2>
                            <div class="space-y-4">
                                @foreach($results['positions'] as $position)
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                                            <a href="{{ route('careers.apply', $position) }}" class="hover:text-indigo-600 transition">
                                                {{ $position->title }}
                                            </a>
                                        </h3>
                                        @if($position->description)
                                            <p class="text-gray-600 mb-4">
                                                {{ Str::limit(strip_tags($position->description), 200) }}
                                            </p>
                                        @endif
                                        <a href="{{ route('careers.apply', $position) }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition text-sm">
                                            Apply Now
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <div class="bg-white rounded-xl p-12 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Start your search</h2>
                    <p class="text-gray-600">Enter a keyword above to search our blog posts and job openings.</p>
                </div>
            @endif
        </div>
    </section>
@endsection

