@extends('layouts.admin')

@section('title', 'Admin - Position Details')
@section('page_title', 'Position Details')
@section('page_description', 'Deep dive into a single open role used on the public careers page.')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.positions.index') }}"
           class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900">
            ← Back to Positions
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.positions.edit', $position) }}"
               class="btn-ghost text-sm">
                Edit position
            </a>
            <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="inline-block delete-form">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl border-2 border-red-200 bg-white text-red-600 text-sm font-semibold hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main details --}}
        <div class="lg:col-span-2 glass-card">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $position->title }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                            {{ $position->type }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-50 text-slate-700 font-medium">
                            {{ $position->location }}
                        </span>
                        @if($position->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right text-xs text-slate-500 space-y-1">
                    <div>Created: {{ $position->created_at?->format('M j, Y') }}</div>
                    <div>Updated: {{ $position->updated_at?->format('M j, Y') }}</div>
                    @if(!is_null($position->order))
                        <div>Order: #{{ $position->order }}</div>
                    @endif
                </div>
            </div>

            <div class="prose prose-sm max-w-none text-slate-800">
                {!! nl2br(e($position->description)) !!}
            </div>
        </div>

        {{-- Meta & tags --}}
        <div class="space-y-4">
            <div class="glass-card">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Role Metadata</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Type</dt>
                        <dd class="text-slate-900">{{ $position->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Location</dt>
                        <dd class="text-slate-900">{{ $position->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Status</dt>
                        <dd class="text-slate-900">
                            {{ $position->is_active ? 'Active (visible on careers page)' : 'Inactive (hidden from careers page)' }}
                        </dd>
                    </div>
                    @if(!empty($position->tags_array))
                        <div>
                            <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Tags</dt>
                            <dd>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($position->tags_array as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-medium">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="glass-card">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Public careers link</h3>
                <p class="text-xs text-slate-600 mb-3">
                    Use this link to view how the role appears on the public careers page.
                </p>
                <a href="{{ route('careers.index') }}#position-{{ $position->id }}"
                   target="_blank"
                   class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700">
                    View on careers page
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14 3h7m0 0v7m0-7L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection


