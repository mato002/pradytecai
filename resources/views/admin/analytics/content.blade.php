@extends('layouts.admin')
@section('title', 'Content Analytics')
@section('page_title', 'Content Analytics')
@section('content')
@include('admin.analytics._nav')
@if(!$hasData)
<div class="glass-card text-center py-12 text-slate-600">No published or scheduled content yet.</div>
@else
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Destinations</th><th class="px-4 py-3">Published</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@foreach($items as $item)
<tr>
<td class="px-4 py-3">{{ $item->title }}</td>
<td class="px-4 py-3">{{ $item->status }}</td>
<td class="px-4 py-3">{{ $item->destinations_count }}</td>
<td class="px-4 py-3">{{ optional($item->published_at)->format('M j, Y') ?? '—' }}</td>
</tr>
@endforeach
</tbody></table>
</div>
@endif
@endsection
