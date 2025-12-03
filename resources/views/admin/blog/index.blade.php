@extends('layouts.admin')

@section('title', 'Admin - Blog')
@section('page_title', 'Blog Posts')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Blog Posts</h2>
            <p class="text-sm text-slate-500">Manage articles that appear on the public blog page.</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            <span class="mr-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </span>
            New Post
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Published</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($posts as $post)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $post->title }}</p>
                            @if($post->excerpt)
                                <p class="text-xs text-slate-500 line-clamp-1">{{ $post->excerpt }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $post->category ?: 'General' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-500">
                            {{ $post->published_at ? $post->published_at->format('M j, Y') : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($post->is_published)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 text-xs">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.blog.edit', $post) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs md:text-sm">
                                Edit
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this post? This cannot be undone.');">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50 text-xs md:text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                            No blog posts yet. Create your first article to populate the blog page.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($posts, 'links'))
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection


