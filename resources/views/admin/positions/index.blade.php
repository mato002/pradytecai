@extends('layouts.admin')

@section('title', 'Admin - Positions')
@section('page_title', 'Open Positions')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header + primary action --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Open Positions</h2>
            <p class="text-sm text-slate-600 mt-1">Manage job positions that appear on the public careers page.</p>
        </div>
        <a href="{{ route('admin.positions.create') }}"
           class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Position
        </a>
    </div>

    {{-- Stats row --}}
    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-4 mb-8">
        <div class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total positions</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['total'] ?? $positions->total() }}</p>
        </div>
        <div class="metric-card">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $stats['active'] ?? $positions->where('is_active', true)->count() }}</p>
        </div>
        <div class="metric-card hidden md:block">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Remote / Hybrid</p>
            <p class="mt-2 text-lg font-semibold text-slate-900">
                {{ $stats['remote'] ?? 0 }} <span class="text-xs text-slate-500">Remote</span>
                ·
                {{ $stats['hybrid'] ?? 0 }} <span class="text-xs text-slate-500">Hybrid</span>
            </p>
        </div>
        <div class="metric-card hidden xl:block">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">On-site roles</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $stats['onsite'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Filters + search --}}
    <div class="glass-card mb-6">
        <form method="GET" action="{{ route('admin.positions.index') }}" class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 flex-1">
                <div>
                    <label for="q" class="block text-xs font-semibold text-slate-600 mb-1">Search</label>
                    <input
                        id="q"
                        name="q"
                        type="text"
                        value="{{ $filters['q'] ?? request('q') }}"
                        placeholder="Search by title, description, tags..."
                        class="admin-form-input"
                    >
                </div>
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select id="status" name="status" class="admin-form-select">
                        @php($status = $filters['status'] ?? request('status', 'active'))
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
                    @php($type = $filters['type'] ?? request('type'))
                    <select id="type" name="type" class="admin-form-select">
                        <option value="">All types</option>
                        <option value="Full-time" {{ $type === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ $type === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Contract" {{ $type === 'Contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div>
                    <label for="location" class="block text-xs font-semibold text-slate-600 mb-1">Location</label>
                    @php($location = $filters['location'] ?? request('location'))
                    <select id="location" name="location" class="admin-form-select">
                        <option value="">All locations</option>
                        <option value="Remote" {{ $location === 'Remote' ? 'selected' : '' }}>Remote</option>
                        <option value="Hybrid" {{ $location === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="On-site" {{ $location === 'On-site' ? 'selected' : '' }}>On-site</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary px-5 py-2.5 text-sm">
                    Apply filters
                </button>
                <a href="{{ route('admin.positions.index') }}" class="btn-ghost px-4 py-2 text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Positions table --}}
    <div class="glass-card overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Location</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Tags</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($positions as $position)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 align-top">
                            <p class="font-semibold text-slate-900">{{ $position->title }}</p>
                            <p class="mt-1 text-xs text-slate-500 line-clamp-1">{{ Str::limit($position->description, 80) }}</p>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                                {{ $position->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-700 text-xs font-medium">
                                {{ $position->location }}
                            </span>
                        </td>
                        <td class="px-4 py-3 align-top">
                            @if(!empty($position->tags_array))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($position->tags_array as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-medium">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-top">
                            @if($position->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap align-top space-x-2">
                            <a href="{{ route('admin.positions.show', $position) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs md:text-sm">
                                View
                            </a>
                            <a href="{{ route('admin.positions.edit', $position) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs md:text-sm">
                                Edit
                            </a>
                            <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="inline-block delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50 text-xs md:text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">
                            No positions yet. Create your first position to populate the careers page.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($positions, 'links'))
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
@endsection



