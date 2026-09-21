@extends('layouts.admin')
@section('title', $campaign->name)
@section('page_title', $campaign->name)
@section('header_actions')
    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-primary">Edit</a>
@endsection
@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="glass-card lg:col-span-2 space-y-3">
        <p><span class="text-slate-500">Status:</span> <strong>{{ $campaign->status }}</strong></p>
        <p><span class="text-slate-500">Objective:</span> {{ $campaign->objective ?: '—' }}</p>
        <p><span class="text-slate-500">Owner:</span> {{ $campaign->owner?->name ?: '—' }}</p>
        <p><span class="text-slate-500">Dates:</span> {{ optional($campaign->starts_at)->format('M j, Y') ?: '—' }} → {{ optional($campaign->ends_at)->format('M j, Y') ?: '—' }}</p>
        <p><span class="text-slate-500">Budget:</span> {{ $campaign->budget_amount !== null ? number_format($campaign->budget_amount, 2) : '—' }}</p>
        <p><span class="text-slate-500">UTM:</span> {{ $campaign->utm_campaign ?: '—' }}</p>
        <p class="text-slate-700 whitespace-pre-wrap">{{ $campaign->audience_notes }}</p>
    </div>
    <div class="glass-card">
        <h3 class="font-bold text-slate-900 mb-3">Products</h3>
        <ul class="space-y-2 text-sm">
            @forelse($campaign->products as $p)
                <li>{{ $p->name }}</li>
            @empty
                <li class="text-slate-500">None linked</li>
            @endforelse
        </ul>
        <h3 class="font-bold text-slate-900 mt-6 mb-3">Recent content</h3>
        <ul class="space-y-2 text-sm">
            @forelse($campaign->contentItems as $item)
                <li><a class="text-indigo-700 hover:underline" href="{{ route('admin.content.edit', $item) }}">{{ $item->title }}</a></li>
            @empty
                <li class="text-slate-500">No content yet</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
