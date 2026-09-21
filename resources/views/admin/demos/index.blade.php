@extends('layouts.admin')
@section('title', 'Demo Requests')
@section('page_title', 'Demo Requests')
@section('header_actions')
@include('admin.partials.product-switcher')
@endsection
@section('content')
<div class="mb-4 glass-card">
<form method="GET" class="flex gap-3">
<select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="all">All statuses</option>
@foreach($statuses as $status)
<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
@endforeach
</select>
<button class="btn-primary">Filter</button>
</form>
</div>
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Product</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Preferred</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($demos as $demo)
<tr>
<td class="px-4 py-3">{{ $demo->contactMessage?->name ?? '—' }}</td>
<td class="px-4 py-3">{{ $demo->product?->name ?? '—' }}</td>
<td class="px-4 py-3">{{ $demo->status }}</td>
<td class="px-4 py-3">{{ optional($demo->preferred_at)->format('M j, Y H:i') ?? '—' }}</td>
<td class="px-4 py-3 text-right"><a href="{{ route('admin.demos.show', $demo) }}" class="btn-ghost">Open</a></td>
</tr>
@empty
<tr><td colspan="5" class="px-4 py-8 text-center text-slate-600">No demo requests.</td></tr>
@endforelse
</tbody></table>
{{ $demos->links() }}
</div>
@endsection
