@extends('layouts.admin')
@section('title', 'Website Analytics')
@section('page_title', 'Website Analytics')
@section('content')
@include('admin.analytics._nav')
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No website events in the last 30 days.</div>
@else
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Event</th><th class="px-4 py-3">Count</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($events as $event)
<tr><td class="px-4 py-3">{{ $event->event_name }}</td><td class="px-4 py-3 font-semibold">{{ number_format($event->total) }}</td></tr>
@endforeach
</tbody></table>
</div>
@endif
@endsection
