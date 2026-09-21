@extends('layouts.admin')
@section('title', 'Social Analytics')
@section('page_title', 'Social Analytics')
@section('content')
@include('admin.analytics._nav')
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No social metric snapshots stored.</div>
@else
<div class="grid gap-4 sm:grid-cols-3 mb-6">
@foreach($aggregates as $key => $total)
<div class="metric-card"><p class="font-semibold">{{ $key }}</p><p class="text-3xl font-bold mt-2">{{ number_format((float)$total) }}</p></div>
@endforeach
</div>
@endif
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Account</th><th class="px-4 py-3">Platform</th><th class="px-4 py-3">Followers</th><th class="px-4 py-3">Snapshots</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($accounts as $account)
<tr><td class="px-4 py-3">{{ $account->name }}</td><td class="px-4 py-3">{{ $account->platform }}</td><td class="px-4 py-3">{{ $account->follower_count !== null ? number_format($account->follower_count) : '—' }}</td><td class="px-4 py-3">{{ $account->metricSnapshots->count() }}</td></tr>
@endforeach
</tbody></table>
</div>
@endsection
