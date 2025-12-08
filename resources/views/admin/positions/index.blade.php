@extends('layouts.admin')

@section('title', 'Admin - Positions')
@section('page_title', 'Open Positions')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Open Positions</h2>
            <p class="text-sm text-slate-500">Manage job positions that appear on the careers page.</p>
        </div>
        <a href="{{ route('admin.positions.create') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            <span class="mr-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </span>
            New Position
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Location</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($positions as $position)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $position->title }}</p>
                            <p class="text-xs text-slate-500 line-clamp-1">{{ Str::limit($position->description, 60) }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $position->type }}
                        </td>
                        <td class="px-4 py-3 text-slate-700">
                            {{ $position->location }}
                        </td>
                        <td class="px-4 py-3">
                            @if($position->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 text-xs">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
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
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
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



