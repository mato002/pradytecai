@extends('layouts.admin')

@section('title', 'Admin - View Product')
@section('page_title', 'View Product')
@section('page_description', 'View product details and information')

@section('content')
    <div class="max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900 mb-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Products
            </a>
        </div>

        <div class="glass-card">
            <!-- Header Section -->
            <div class="border-b border-slate-200 pb-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $product->name }}</h2>
                        @if($product->type)
                            <p class="text-sm text-slate-600">{{ $product->type }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if($product->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Description</h3>
                    <p class="text-slate-900 whitespace-pre-wrap">{{ $product->description }}</p>
                </div>
            @endif

            <!-- URL -->
            @if($product->url)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Product URL</h3>
                    <a href="{{ $product->url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700 hover:underline inline-flex items-center gap-2">
                        {{ $product->url }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            @endif

            <!-- Features -->
            @if($product->features && count($product->features) > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Features</h3>
                    <ul class="space-y-2">
                        @foreach($product->features as $feature)
                            @if($feature)
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-slate-900">{{ $feature }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Benefits -->
            @if($product->benefits && count($product->benefits) > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Benefits</h3>
                    <ul class="space-y-2">
                        @foreach($product->benefits as $benefit)
                            @if($benefit)
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-slate-900">{{ $benefit }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Statistics -->
            @if($product->statistics && count($product->statistics) > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Statistics</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($product->statistics as $key => $value)
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <div class="text-2xl font-bold text-indigo-600 mb-1">{{ $value }}</div>
                                <div class="text-sm text-slate-600 uppercase tracking-wide">{{ $key }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            <div class="grid md:grid-cols-2 gap-6 pt-6 border-t border-slate-200">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Display Settings</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-slate-500 mb-1">Button Text</dt>
                            <dd class="text-slate-900">{{ $product->button_text ?? 'Learn More' }}</dd>
                        </div>
                        @if($product->icon)
                            <div>
                                <dt class="text-xs text-slate-500 mb-1">Icon</dt>
                                <dd class="text-slate-900">{{ $product->icon }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-slate-500 mb-1">Display Order</dt>
                            <dd class="text-slate-900">{{ $product->order ?? 'Not set' }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Metadata</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs text-slate-500 mb-1">Created</dt>
                            <dd class="text-slate-900">{{ $product->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500 mb-1">Last Updated</dt>
                            <dd class="text-slate-900">{{ $product->updated_at->format('M j, Y g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-200 mt-6">
                <a href="{{ route('admin.products.index') }}" class="btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Products
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Product
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection



