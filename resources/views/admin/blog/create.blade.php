@extends('layouts.admin')

@section('title', 'Admin - New Blog Post')
@section('page_title', 'New Blog Post')
@section('page_eyebrow', 'Content Lab')
@section('page_description', 'Create a new blog post to publish on your website')

@section('content')
    <div class="max-w-4xl">
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="admin-form-card">
            <form action="{{ route('admin.blog.store') }}" method="POST" class="admin-form-section">
                @csrf

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="admin-form-input"
                           placeholder="Enter a compelling title for your blog post"
                           required>
                    @error('title')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                               class="admin-form-input"
                               placeholder="auto-generated-from-title">
                        <p class="admin-form-help">Leave empty to auto-generate from title</p>
                        @error('slug')
                            <p class="admin-form-error">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}"
                               class="admin-form-input"
                               placeholder="e.g. Product Updates, Best Practices">
                        <p class="admin-form-help">Optional category for organizing posts</p>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Short Excerpt</label>
                    <textarea name="excerpt" rows="3"
                              class="admin-form-textarea"
                              placeholder="A brief summary that appears on the blog listing page...">{{ old('excerpt') }}</textarea>
                    <p class="admin-form-help">This will be shown as the preview text on the blog grid</p>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Body Content</label>
                    <textarea name="body" rows="12"
                              class="admin-form-textarea font-mono text-sm"
                              placeholder="Write your blog post content here. You can use markdown or HTML..."
                              required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-divider"></div>

                <div class="admin-form-section-title">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Publishing Settings
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Published At</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                               class="admin-form-input">
                        <p class="admin-form-help">Schedule when this post should be published</p>
                    </div>
                    <div class="admin-form-group flex items-center pt-8">
                        <input type="checkbox" id="is_published" name="is_published" value="1"
                               class="admin-form-checkbox"
                               {{ old('is_published', true) ? 'checked' : '' }}>
                        <label for="is_published" class="ml-3 text-sm font-medium text-slate-700 cursor-pointer">
                            Publish immediately
                        </label>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save & Publish Post
                    </button>
                    <a href="{{ route('admin.blog.index') }}" class="admin-btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
