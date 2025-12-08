@extends('layouts.admin')

@section('title', 'Admin - New Position')
@section('page_title', 'New Position')
@section('page_eyebrow', 'Hiring Board')
@section('page_description', 'Create a new job position to attract talented candidates')

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
            <form action="{{ route('admin.positions.store') }}" method="POST" class="admin-form-section">
                @csrf

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Position Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="admin-form-input"
                           placeholder="e.g. Senior Software Developer, Product Manager"
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

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Job Description</label>
                    <textarea name="description" rows="8"
                              class="admin-form-textarea"
                              placeholder="Describe the position, responsibilities, requirements, and what you're looking for in a candidate..."
                              required>{{ old('description') }}</textarea>
                    <p class="admin-form-help">Provide a comprehensive description to attract the right candidates</p>
                    @error('description')
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Position Details
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label admin-form-label-required">Employment Type</label>
                        <select name="type" class="admin-form-select" required>
                            <option value="">Select type...</option>
                            <option value="Full-time" {{ old('type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ old('type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ old('type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                        @error('type')
                            <p class="admin-form-error">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label admin-form-label-required">Location Type</label>
                        <select name="location" class="admin-form-select" required>
                            <option value="">Select location...</option>
                            <option value="Remote" {{ old('location') == 'Remote' ? 'selected' : '' }}>Remote</option>
                            <option value="Hybrid" {{ old('location') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="On-site" {{ old('location') == 'On-site' ? 'selected' : '' }}>On-site</option>
                        </select>
                        @error('location')
                            <p class="admin-form-error">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Skills & Tags</label>
                    <input type="text" name="tags" value="{{ old('tags') }}"
                           class="admin-form-input"
                           placeholder="e.g. Laravel, AWS, React, Docker">
                    <p class="admin-form-help">Separate multiple tags with commas (e.g., Laravel, AWS, Docker)</p>
                    @error('tags')
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Display Settings
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                               class="admin-form-input"
                               placeholder="0">
                        <p class="admin-form-help">Lower numbers appear first on the careers page</p>
                    </div>
                    <div class="admin-form-group flex items-center pt-8">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               class="admin-form-checkbox"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700 cursor-pointer">
                            <span class="font-semibold">Active</span>
                            <span class="text-slate-500"> - Visible on careers page</span>
                        </label>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Position
                    </button>
                    <a href="{{ route('admin.positions.index') }}" class="admin-btn-secondary">
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
