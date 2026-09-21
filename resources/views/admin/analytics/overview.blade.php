@extends('layouts.admin')
@section('title', 'Analytics Overview')
@section('page_title', 'Analytics Overview')
@section('header_actions')
@include('admin.partials.product-switcher')
@endsection
@section('content')
<div class="mb-4 flex flex-wrap gap-2">
@foreach(['overview'=>'Overview','social'=>'Social','website'=>'Website','campaigns'=>'Campaigns','products'=>'Products','content'=>'Content'] as $route=>$label)
<a href="{{ route('admin.analytics.'.$route) }}" class="nav-pill {{ request()->routeIs('admin.analytics.'.$route) ? 'nav-pill--active' : '' }}">{{ $label }}</a>
@endforeach
</div>
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No analytics data yet. Metrics appear after syncs and website events.</div>
@else
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
@foreach($aggregates as $row)
<div class="metric-card">
<p class="font-semibold text-slate-800">{{ $row->metric_key }}</p>
<p class="mt-3 text-3xl font-bold">{{ number_format((float)$row->total) }}</p>
<p class="text-sm text-slate-600">{{ $row->samples }} samples</p>
</div>
@endforeach
@foreach($eventCounts as $name => $total)
<div class="metric-card">
<p class="font-semibold text-slate-800">Event: {{ $name }}</p>
<p class="mt-3 text-3xl font-bold">{{ number_format($total) }}</p>
<p class="text-sm text-slate-600">Last 30 days</p>
</div>
@endforeach
</div>
@endif
@endsection
