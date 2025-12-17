@extends('layouts.admin')

@section('title', 'Admin - Edit Product')
@section('page_title', 'Edit Product')
@section('page_description', 'Update product information and settings')

@section('content')
    <div class="max-w-4xl">
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 rounded-2xl border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-emerald-900">Success!</p>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-red-900 mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="glass-card">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                       class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Features (one per line)</label>
                <textarea name="features_text" rows="6"
                          class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none">{{ old('features_text', $product->features ? implode("\n", $product->features) : '') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Enter each feature on a new line</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Benefits (one per line)</label>
                <textarea name="benefits_text" rows="6"
                          class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none">{{ old('benefits_text', $product->benefits ? implode("\n", $product->benefits) : '') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Enter each benefit on a new line</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Statistics (JSON format)</label>
                <textarea name="statistics_json" rows="4"
                          class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none">{{ old('statistics_json', $product->statistics ? json_encode($product->statistics, JSON_PRETTY_PRINT) : '') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Enter statistics as JSON. Example: {"stat1_label": "99.9%", "stat1_value": "Delivery Rate"}</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Type</label>
                    <input type="text" name="type" value="{{ old('type', $product->type) }}"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="e.g., Messaging Platform">
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Product URL</label>
                    <input type="url" name="url" value="{{ old('url', $product->url) }}"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="https://example.com">
                    @error('url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Button Text</label>
                    <input type="text" name="button_text" value="{{ old('button_text', $product->button_text ?? 'Learn More') }}"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="e.g. Learn More, Open CRM">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Icon</label>
                    <input type="text" name="icon" value="{{ old('icon', $product->icon) }}"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="e.g. messaging, finance">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', $product->order) }}" min="0"
                           class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    <p class="mt-1 text-xs text-slate-500">Lower numbers appear first</p>
                </div>
                <div class="flex items-center pt-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm font-medium text-slate-700">Active (visible on products page)</span>
                    </label>
                </div>
            </div>

                <div class="flex items-center justify-between pt-6 border-t border-slate-200">
                    <a href="{{ route('admin.products.index') }}" class="btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


