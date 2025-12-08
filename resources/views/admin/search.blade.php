@extends('layouts.admin')
@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Admin - Search')
@section('page_title', 'Search Results')
@section('page_eyebrow', 'Workspace search')

@section('content')
    <!-- Search Bar -->
    <div class="mb-6">
        <form action="{{ route('admin.search') }}" method="GET" class="flex items-center gap-3 rounded-xl border-2 border-slate-300 bg-white px-5 py-3 hover:border-indigo-400 focus-within:border-indigo-500 transition shadow-sm">
            <svg class="w-6 h-6 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input 
                type="text" 
                name="q"
                placeholder="Search blog posts, users, enquiries, applications..." 
                value="{{ $query }}"
                class="bg-transparent text-base text-slate-900 placeholder-slate-500 focus:outline-none flex-1 min-w-0"
            >
            <button type="submit" class="btn-primary" title="Search">
                Search
            </button>
        </form>
    </div>

    @if(empty($query))
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <p class="text-lg text-slate-700">Enter a search term to find content across your workspace</p>
        </div>
    @else
        @php
            $totalResults = ($results['blog_posts']->count() ?? 0) + 
                          ($results['users']->count() ?? 0) + 
                          ($results['enquiries']->count() ?? 0) + 
                          ($results['applications']->count() ?? 0) + 
                          ($results['positions']->count() ?? 0);
        @endphp

        <div class="mb-6">
            <p class="text-base text-slate-700">
                Found <span class="font-semibold text-slate-900">{{ $totalResults }}</span> result{{ $totalResults !== 1 ? 's' : '' }} for "<span class="font-semibold text-slate-900">{{ $query }}</span>"
            </p>
        </div>

        <div class="space-y-6">
            <!-- Blog Posts -->
            @if(isset($results['blog_posts']) && $results['blog_posts']->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Blog Posts ({{ $results['blog_posts']->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($results['blog_posts'] as $post)
                            <a href="{{ route('admin.blog.edit', $post) }}" class="block p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $post->title }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ Str::limit($post->excerpt ?? $post->body, 100) }}</p>
                                <div class="mt-2 flex items-center gap-3 text-xs text-slate-500">
                                    <span>{{ $post->category ?? 'Uncategorized' }}</span>
                                    <span>•</span>
                                    <span>{{ $post->is_published ? 'Published' : 'Draft' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Users -->
            @if(isset($results['users']) && $results['users']->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Users ({{ $results['users']->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($results['users'] as $user)
                            <a href="{{ route('admin.users.edit', $user) }}" class="block p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $user->name }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ $user->email }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Enquiries -->
            @if(isset($results['enquiries']) && $results['enquiries']->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Enquiries ({{ $results['enquiries']->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($results['enquiries'] as $enquiry)
                            <div class="p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $enquiry->name }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ $enquiry->email }}</p>
                                <p class="text-sm text-slate-700 mt-2">{{ $enquiry->subject }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $enquiry->created_at->format('M j, Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Applications -->
            @if(isset($results['applications']) && $results['applications']->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Job Applications ({{ $results['applications']->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($results['applications'] as $application)
                            <a href="{{ route('admin.applications.show', $application) }}" class="block p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $application->name }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ $application->email }}</p>
                                <p class="text-sm text-slate-700 mt-2">{{ $application->position->title ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $application->created_at->format('M j, Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Positions -->
            @if(isset($results['positions']) && $results['positions']->count() > 0)
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Positions ({{ $results['positions']->count() }})</h2>
                    <div class="space-y-3">
                        @foreach($results['positions'] as $position)
                            <a href="{{ route('admin.positions.edit', $position) }}" class="block p-4 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $position->title }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ Str::limit($position->description, 100) }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $position->is_active ? 'Active' : 'Inactive' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($totalResults === 0)
                <div class="bg-white border border-slate-200 rounded-xl p-12 text-center">
                    <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg text-slate-700">No results found for "<span class="font-semibold">{{ $query }}</span>"</p>
                    <p class="text-sm text-slate-500 mt-2">Try different keywords or check your spelling</p>
                </div>
            @endif
        </div>
    @endif
@endsection

