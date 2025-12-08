@extends('layouts.admin')
@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Admin Dashboard - Pradytecai')
@section('page_title', 'Command Overview')
@section('page_eyebrow', 'Intelligence layer')
@section('page_description', 'Live snapshot across products, enquiries, and hiring so you can respond before signals turn into bottlenecks.')

@section('header_actions')
    <a href="{{ route('admin.products.index') }}" class="btn-ghost">
        Manage products
    </a>
    <a href="{{ route('admin.blog.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v16m8-8H4" />
        </svg>
        Publish update
    </a>
@endsection

@section('content')
    @php
        $statCards = [
            [
                'label' => 'Total users',
                'value' => number_format($stats['total_users'] ?? 0),
                'meta' => 'Admin identities with access',
                'trend' => '+4.3%',
                'spark' => [24, 32, 28, 44, 39, 52, 48],
            ],
            [
                'label' => 'Active products',
                'value' => number_format($stats['active_products'] ?? 0),
                'meta' => 'Live on marketing site',
                'trend' => '+1 launch',
                'spark' => [12, 18, 14, 20, 26, 22, 30],
            ],
            [
                'label' => 'New enquiries (7d)',
                'value' => number_format($stats['new_enquiries'] ?? 0),
                'meta' => 'Inbound conversations',
                'trend' => '+12%',
                'spark' => [10, 14, 18, 12, 20, 26, 22],
            ],
            [
                'label' => 'Published posts',
                'value' => number_format($stats['published_posts'] ?? 0),
                'meta' => 'Articles live on blog',
                'trend' => '+2 drafts',
                'spark' => [6, 8, 7, 9, 11, 10, 12],
            ],
        ];
    @endphp

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statCards as $card)
            <div class="metric-card">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold text-slate-800">{{ $card['label'] }}</p>
                    <span class="stat-trend stat-trend--up text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 10l4-4 4 4 6-6" />
                        </svg>
                        {{ $card['trend'] }}
                    </span>
                </div>
                <p class="mt-6 text-5xl font-bold text-slate-900">{{ $card['value'] }}</p>
                <p class="mt-2 text-sm font-medium text-slate-700">{{ $card['meta'] }}</p>
                <div class="metric-card__spark">
                    @foreach ($card['spark'] as $height)
                        <span style="height: {{ $height }}px"></span>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <div class="glass-card lg:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Enquiry activity</h2>
                    <p class="mt-1 text-base text-slate-700">Last {{ $recent_enquiries->count() ? $recent_enquiries->count() : 'few' }} signals</p>
                </div>
                <span class="badge-soft">Live feed</span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($recent_enquiries as $enquiry)
                    <div class="flex items-start justify-between border-b border-slate-200 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-start">
                            <span class="timeline-dot"></span>
                            <div>
                                <p class="text-lg font-bold text-slate-900">{{ $enquiry->name ?? 'Anonymous' }}</p>
                                <p class="mt-1 text-base text-slate-700">
                                    {{ $enquiry->subject ?? $enquiry->company ?? 'General enquiry' }}
                                </p>
                                <p class="mt-2 max-w-lg text-sm text-slate-600">{{ Str::limit($enquiry->message ?? '—', 80) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-slate-700">{{ optional($enquiry->created_at)->diffForHumans() }}</p>
                            <a href="{{ route('admin.enquiries.index') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-700 underline-offset-4 hover:underline hover:text-indigo-800">Open</a>
                        </div>
                    </div>
                @empty
                    <p class="text-base text-slate-700">You're all caught up. New enquiries will stream here.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card">
                <h2 class="text-xl font-bold text-slate-900">Quick actions</h2>
                <div class="mt-6 space-y-4">
                    <a href="{{ route('admin.enquiries.index') }}" class="quick-link">
                        <span class="font-semibold">Review inbox</span>
                        <span class="text-sm text-slate-600">2m avg</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="quick-link">
                        <span class="font-semibold">Invite teammate</span>
                        <span class="text-sm text-slate-600">Role based</span>
                    </a>
                    <a href="{{ route('admin.positions.index') }}" class="quick-link">
                        <span class="font-semibold">Update hiring</span>
                        <span class="text-sm text-slate-600">{{ $stats['active_positions'] ?? 0 }} roles</span>
                    </a>
                </div>
            </div>

            <div class="glass-card">
                <h2 class="text-xl font-bold text-slate-900">Operational health</h2>
                <div class="mt-6 space-y-6 text-base">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="font-semibold text-slate-800">Hiring pipeline</p>
                            <span class="text-sm font-medium text-slate-700">{{ $stats['active_positions'] ?? 0 }} active</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-slate-200">
                            <span class="block h-2 rounded-full bg-gradient-to-r from-amber-400 via-pink-500 to-purple-500"
                                  style="width: {{ min(($stats['active_positions'] ?? 0) * 10, 100) }}%"></span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="font-semibold text-slate-800">Content cadence</p>
                            <span class="text-sm font-medium text-slate-700">{{ $stats['published_posts'] ?? 0 }} live</span>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-slate-200">
                            <span class="block h-2 rounded-full bg-gradient-to-r from-sky-400 to-indigo-500"
                                  style="width: {{ min(($stats['published_posts'] ?? 0) * 8, 100) }}%"></span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="font-semibold text-slate-800">Enquiry response SLA</p>
                            <span class="text-sm font-semibold text-emerald-700">↑ healthy</span>
                        </div>
                        <div class="mt-3 flex items-baseline gap-3">
                            <p class="text-4xl font-bold text-slate-900">2h</p>
                            <p class="text-sm font-medium text-slate-700">average first reply</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


