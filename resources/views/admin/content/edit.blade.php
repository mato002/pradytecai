@extends('layouts.admin')
@section('title', 'Edit Content')
@section('page_title', 'Edit: '.$item->title)
@section('content')
@if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif
<div class="grid gap-6 lg:grid-cols-3">
<div class="glass-card lg:col-span-2">
<form method="POST" action="{{ route('admin.content.update', $item) }}" class="space-y-4">@csrf
@include('admin.content._form')
<button class="btn-primary">Save</button>
</form>
</div>
<div class="glass-card space-y-3">
<p class="text-sm text-slate-600">Status: <strong>{{ $item->status }}</strong></p>
@can('content.edit')
@if($item->status === 'draft')
<form method="POST" action="{{ route('admin.content.submit', $item) }}">@csrf<button class="btn-ghost w-full">Submit for review</button></form>
@endif
@endcan
@can('content.approve')
@if($item->status === 'in_review')
<form method="POST" action="{{ route('admin.content.approve', $item) }}">@csrf<button class="btn-primary w-full">Approve</button></form>
@endif
@endcan
@can('content.publish')
<form method="POST" action="{{ route('admin.content.schedule', $item) }}" class="space-y-2">@csrf
<input type="datetime-local" name="scheduled_at" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
<button class="btn-ghost w-full">Schedule</button>
</form>
<form method="POST" action="{{ route('admin.content.publish', $item) }}">@csrf<button class="btn-primary w-full">Publish now</button></form>
@endcan
<form method="POST" action="{{ route('admin.content.destroy', $item) }}">@csrf<button class="btn-ghost w-full text-red-600" onclick="return confirm('Delete?')">Delete</button></form>
</div>
</div>
@endsection
