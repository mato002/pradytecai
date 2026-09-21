@extends('layouts.admin')
@section('title', 'Marketing Pulse')
@section('page_title', 'Marketing Pulse')
@section('header_actions')
@include('admin.partials.product-switcher')
@endsection
@section('content')
<div class="mb-4 glass-card">
<form method="GET" class="flex flex-wrap gap-3">
<select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
@foreach(['open','resolved','dismissed','all'] as $s)
<option value="{{ $s }}" @selected(request('status','open')===$s)>{{ ucfirst($s) }}</option>
@endforeach
</select>
<select name="severity" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="all">All severities</option>
<option value="critical" @selected(request('severity')==='critical')>Critical</option>
<option value="warning" @selected(request('severity')==='warning')>Warning</option>
</select>
<button class="btn-primary">Filter</button>
</form>
</div>
<div class="space-y-3">
@forelse($alerts as $alert)
<div class="glass-card">
<div class="flex flex-wrap items-start justify-between gap-3">
<div>
<p class="text-lg font-bold text-slate-900">{{ $alert->title }}</p>
<p class="mt-1 text-slate-700">{{ $alert->message }}</p>
<p class="mt-2 text-xs uppercase tracking-wide text-slate-500">{{ $alert->rule_key }} · {{ $alert->severity }} · {{ $alert->status }}</p>
</div>
<div class="text-right space-y-2">
<p class="text-sm text-slate-600">{{ optional($alert->triggered_at)->diffForHumans() }}</p>
@if($alert->status === 'open')
<form method="POST" action="{{ route('admin.pulse.resolve', $alert) }}">@csrf<button class="btn-ghost">Resolve</button></form>
@endif
</div>
</div>
</div>
@empty
<div class="glass-card text-center py-10 text-slate-600">No alerts match these filters.</div>
@endforelse
</div>
<div class="mt-4">{{ $alerts->links() }}</div>
@endsection
