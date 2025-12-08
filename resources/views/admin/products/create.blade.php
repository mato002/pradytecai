@extends('layouts.admin')

@section('title', 'Admin - New Product')
@section('page_title', 'New Product')
@section('page_eyebrow', 'Solutions Catalog')
@section('page_description', 'Add a new product to showcase on your marketing site')

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
            <form action="{{ route('admin.products.store') }}" method="POST" class="admin-form-section">
                @csrf

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Product Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="admin-form-input"
                           placeholder="e.g. BulkSMS CRM, Microfinance System"
                           required>
                    @error('name')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Description</label>
                    <textarea name="description" rows="5"
                              class="admin-form-textarea"
                              placeholder="Describe the product, its key features, and benefits...">{{ old('description') }}</textarea>
                    <p class="admin-form-help">Provide a detailed description that will help visitors understand your product</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Additional Information
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Product Type</label>
                        <input type="text" name="type" value="{{ old('type') }}"
                               class="admin-form-input"
                               placeholder="e.g. Messaging Platform, Microfinance System">
                        <p class="admin-form-help">Category or type of product</p>
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
                        <label class="admin-form-label">Product URL</label>
                        <input type="url" name="url" value="{{ old('url') }}"
                               class="admin-form-input"
                               placeholder="https://example.com/product">
                        <p class="admin-form-help">Link to product page or website</p>
                        @error('url')
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
                    <label class="admin-form-label">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="admin-form-input w-32"
                           placeholder="0">
                    <p class="admin-form-help">Lower numbers appear first in listings</p>
                    @error('order')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-divider"></div>

                <div class="admin-form-group">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               class="admin-form-checkbox"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3 text-sm font-medium text-slate-700 cursor-pointer">
                            <span class="font-semibold">Active</span>
                            <span class="text-slate-500"> - Visible on the products page</span>
                        </label>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">
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
