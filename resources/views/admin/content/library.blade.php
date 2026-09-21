@extends('layouts.admin')
@section('title', 'Content Library')
@section('page_title', 'Content Library')
@section('header_actions')
@include('admin.partials.product-switcher')
<a href="{{ route('admin.content.calendar') }}" class="btn-ghost">Calendar</a>
<a href="{{ route('admin.content.approvals') }}" class="btn-ghost">Approvals</a>
<a href="{{ route('admin.content.create') }}" class="btn-primary">New content</a>
@endsection
@section('content')

<div class="mb-4 glass-card">
<form method="GET" class="flex flex-wrap gap-3">
<input name="search" value="{{ request('search') }}" placeholder="Search" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="all">All</option>
@foreach(['draft','in_review','approved','scheduled','published','rejected'] as $s)
<option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
@endforeach
</select>
<button class="btn-primary">Filter</button>
</form>
</div>
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Product</th><th class="px-4 py-3">Scheduled</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($items as $item)
<tr>
<td class="px-4 py-3 font-semibold"><a href="{{ route('admin.content.edit', $item) }}" class="hover:underline">{{ $item->title }}</a></td>
<td class="px-4 py-3">{{ $item->status }}</td>
<td class="px-4 py-3">{{ $item->product?->name ?? '—' }}</td>
<td class="px-4 py-3">{{ optional($item->scheduled_at)->format('M j, H:i') ?? '—' }}</td>
<td class="px-4 py-3 text-right"><a class="btn-ghost" href="{{ route('admin.content.edit', $item) }}">Open</a></td>
</tr>
@empty
<tr><td colspan="5" class="px-4 py-8 text-center text-slate-600">No content items.</td></tr>
@endforelse
</tbody>
</table>
{{ $items->links() }}
</div>
@endsection
