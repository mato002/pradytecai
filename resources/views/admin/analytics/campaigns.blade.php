@extends('layouts.admin')
@section('title', 'Campaign Analytics')
@section('page_title', 'Campaign Analytics')
@section('content')
@include('admin.analytics._nav')
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No campaign analytics available yet.</div>
@else
@if($aggregates->isNotEmpty())
<div class="grid gap-4 sm:grid-cols-3 mb-6">
@foreach($aggregates as $key=>$total)
<div class="metric-card"><p class="font-semibold">{{ $key }}</p><p class="text-3xl font-bold mt-2">{{ number_format((float)$total) }}</p></div>
@endforeach
</div>
@endif
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Campaign</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Content</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($campaigns as $campaign)
<tr><td class="px-4 py-3">{{ $campaign->name }}</td><td class="px-4 py-3">{{ $campaign->status }}</td><td class="px-4 py-3">{{ $campaign->content_items_count }}</td></tr>
@endforeach
</tbody></table>
</div>
@endif
@endsection
