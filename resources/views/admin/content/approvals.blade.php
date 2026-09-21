@extends('layouts.admin')
@section('title', 'Content Approvals')
@section('page_title', 'Awaiting Approval')
@section('content')
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Author</th><th class="px-4 py-3">Product</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($items as $item)
<tr>
<td class="px-4 py-3 font-semibold">{{ $item->title }}</td>
<td class="px-4 py-3">{{ $item->author?->name ?? '—' }}</td>
<td class="px-4 py-3">{{ $item->product?->name ?? '—' }}</td>
<td class="px-4 py-3 text-right space-x-2">
<a href="{{ route('admin.content.edit', $item) }}" class="btn-ghost">Review</a>
@can('content.approve')
<form class="inline" method="POST" action="{{ route('admin.content.approve', $item) }}">@csrf<button class="btn-primary">Approve</button></form>
@endcan
</td>
</tr>
@empty
<tr><td colspan="4" class="px-4 py-8 text-center text-slate-600">Nothing awaiting approval.</td></tr>
@endforelse
</tbody>
</table>
{{ $items->links() }}
</div>
@endsection
