@extends('layouts.admin')
@section('title', 'Demo Request')
@section('page_title', 'Demo Request')
@section('content')
@if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="grid gap-6 lg:grid-cols-2">
<div class="glass-card space-y-2">
<p><span class="text-slate-500">Contact:</span> {{ $demo->contactMessage?->name }} ({{ $demo->contactMessage?->email }})</p>
<p><span class="text-slate-500">Product:</span> {{ $demo->product?->name ?? '—' }}</p>
<p><span class="text-slate-500">Preferred:</span> {{ optional($demo->preferred_at)->toDayDateTimeString() ?? '—' }}</p>
<p><span class="text-slate-500">Scheduled:</span> {{ optional($demo->scheduled_at)->toDayDateTimeString() ?? '—' }}</p>
<p><span class="text-slate-500">Assignee:</span> {{ $demo->assignee?->name ?? '—' }}</p>
<p class="whitespace-pre-wrap text-slate-700">{{ $demo->notes }}</p>
</div>
@can('demo_requests.manage')
<div class="glass-card">
<form method="POST" action="{{ route('admin.demos.update', $demo) }}" class="space-y-3">@csrf
<select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
@foreach($statuses as $status)
<option value="{{ $status }}" @selected($demo->status===$status)>{{ $status }}</option>
@endforeach
</select>
<input type="datetime-local" name="scheduled_at" value="{{ optional($demo->scheduled_at)->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<textarea name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Notes">{{ $demo->notes }}</textarea>
<button class="btn-primary">Update</button>
</form>
</div>
@endcan
</div>
@endsection
