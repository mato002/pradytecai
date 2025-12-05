@extends('layouts.admin')

@section('title', 'Admin - New Position')
@section('page_title', 'New Position')

@section('content')
    <div class="max-w-3xl">
        <form action="{{ route('admin.positions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Position Title *</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g. Senior Software Developer"
                       required>
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Describe the position and what you're looking for..."
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Employment Type *</label>
                    <select name="type"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                        <option value="Full-time" {{ old('type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ old('type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Contract" {{ old('type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location Type *</label>
                    <select name="location"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                        <option value="Remote" {{ old('location') == 'Remote' ? 'selected' : '' }}>Remote</option>
                        <option value="Hybrid" {{ old('location') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="On-site" {{ old('location') == 'On-site' ? 'selected' : '' }}>On-site</option>
                    </select>
                    @error('location')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tags (comma-separated)</label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g. Laravel, AWS, React">
                <p class="mt-1 text-xs text-slate-500">Separate multiple tags with commas (e.g., Laravel, AWS, Docker)</p>
                @error('tags')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="0">
                    <p class="mt-1 text-xs text-slate-500">Lower numbers appear first. Default: 0</p>
                </div>
                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-slate-700">Active (visible on careers page)</label>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-indigo-600 text-white text-sm md:text-base font-semibold hover:bg-indigo-700 transition">
                    Save Position
                </button>
                <a href="{{ route('admin.positions.index') }}" class="text-sm text-slate-600 hover:underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection



