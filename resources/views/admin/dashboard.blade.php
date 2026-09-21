@extends('layouts.admin')

@section('title', 'Marketing Dashboard - Pradytecai')
@section('page_title', 'Marketing Command Centre')
@section('page_eyebrow', 'Live overview')
@section('page_description', 'Real signals across campaigns, content, leads, and pulse alerts. No invented trends.')

@section('header_actions')
    @include('admin.partials.product-switcher')
    <a href="{{ route('admin.content.create') }}" class="btn-primary">New content</a>
@endsection

@section('content')
    @php
        $metricCards = [
            ['label' => 'Campaigns running', 'value' => $stats['campaigns_running'] ?? 0, 'meta' => 'Status = active'],
            ['label' => 'Content scheduled (7d)', 'value' => $stats['content_scheduled_7d'] ?? 0, 'meta' => 'Upcoming posts'],
            ['label' => 'Posts published (7d)', 'value' => $stats['posts_published_7d'] ?? 0, 'meta' => 'Destinations published'],
            ['label' => 'Leads (7d)', 'value' => $stats['leads_last_7d'] ?? 0, 'meta' => 'Inbound + demos: '.($stats['demos_last_7d'] ?? 0)],
            ['label' => 'Qualified leads', 'value' => $stats['qualified_leads'] ?? 0, 'meta' => 'Pipeline status'],
            ['label' => 'Awaiting follow-up', 'value' => $stats['leads_awaiting_follow_up'] ?? 0, 'meta' => 'Overdue next_follow_up_at'],
            ['label' => 'Overdue tasks', 'value' => $stats['overdue_tasks'] ?? 0, 'meta' => 'Open past due'],
            ['label' => 'Failed destinations', 'value' => $stats['failed_destinations'] ?? 0, 'meta' => 'Publish failures'],
            ['label' => 'Critical alerts', 'value' => $stats['alerts_critical'] ?? 0, 'meta' => 'Warnings: '.($stats['alerts_warning'] ?? 0)],
            ['label' => 'Inactive products', 'value' => $stats['inactive_products'] ?? 0, 'meta' => '14d / no content'],
            ['label' => 'Inactive social', 'value' => $stats['inactive_social_accounts'] ?? 0, 'meta' => 'No post in 5d'],
            [
                'label' => 'Avg first response',
                'value' => $stats['avg_first_response_hours'] !== null ? $stats['avg_first_response_hours'].'h' : '—',
                'meta' => 'From first_responded_at',
            ],
        ];
    @endphp

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($metricCards as $card)
            <div class="metric-card">
                <p class="text-base font-semibold text-slate-800">{{ $card['label'] }}</p>
                <p class="mt-4 text-4xl font-bold text-slate-900">{{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}</p>
                <p class="mt-2 text-sm font-medium text-slate-600">{{ $card['meta'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-12">
        <div class="glass-card lg:col-span-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Critical pulse alerts</h2>
                    <p class="mt-1 text-base text-slate-700">Open critical alerts (max 8)</p>
                </div>
                <a href="{{ route('admin.pulse.index') }}" class="btn-ghost">Open pulse</a>
            </div>
            <div class="mt-6 space-y-4">
                @forelse ($criticalAlerts as $alert)
                    <div class="flex items-start justify-between border-b border-slate-200 pb-4 last:border-0 last:pb-0">
                        <div>
                            <p class="text-lg font-bold text-slate-900">{{ $alert->title }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $alert->message }}</p>
                            <p class="mt-2 text-xs uppercase tracking-wide text-slate-500">{{ $alert->rule_key }}</p>
                        </div>
                        <p class="text-sm text-slate-600">{{ optional($alert->triggered_at)->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-base text-slate-700">No open critical alerts.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card">
                <h2 class="text-xl font-bold text-slate-900">Metric snapshots</h2>
                <p class="mt-1 text-sm text-slate-600">Sums from stored snapshots only</p>
                <div class="mt-6 space-y-4 text-base">
                    <div class="flex justify-between"><span class="text-slate-700">Reach</span><span class="font-semibold text-slate-900">{{ $stats['metric_reach'] !== null ? number_format($stats['metric_reach']) : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-700">Impressions</span><span class="font-semibold text-slate-900">{{ $stats['metric_impressions'] !== null ? number_format($stats['metric_impressions']) : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-700">Engagement</span><span class="font-semibold text-slate-900">{{ $stats['metric_engagement'] !== null ? number_format($stats['metric_engagement']) : '—' }}</span></div>
                </div>
                @if($stats['metric_reach'] === null && $stats['metric_impressions'] === null && $stats['metric_engagement'] === null)
                    <p class="mt-4 text-sm text-slate-500">No metric snapshots yet.</p>
                @endif
            </div>

            <div class="glass-card">
                <h2 class="text-xl font-bold text-slate-900">Quick links</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.enquiries.index') }}" class="quick-link"><span class="font-semibold">Leads</span></a>
                    <a href="{{ route('admin.campaigns.index') }}" class="quick-link"><span class="font-semibold">Campaigns</span></a>
                    <a href="{{ route('admin.content.calendar') }}" class="quick-link"><span class="font-semibold">Content calendar</span></a>
                    <a href="{{ route('admin.analytics.overview') }}" class="quick-link"><span class="font-semibold">Analytics</span></a>
                </div>
            </div>
        </div>
    </div>
@endsection
