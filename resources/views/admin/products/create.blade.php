@extends('layouts.admin')

@section('title', 'Admin - New Product')
@section('page_title', 'New Product')

@section('content')
    <div class="max-w-3xl">
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Product Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g. BulkSMS CRM"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Describe the product and its features...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                    <input type="text" name="type" value="{{ old('type') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="e.g. Messaging Platform, Microfinance System">
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Product URL</label>
                    <input type="url" name="url" value="{{ old('url') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="https://example.com">
                    @error('url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-slate-700">Active (visible on products page)</label>
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-indigo-600 text-white text-sm md:text-base font-semibold hover:bg-indigo-700 transition">
                    Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-600 hover:underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection


