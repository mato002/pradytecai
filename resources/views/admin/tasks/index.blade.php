@extends('layouts.admin')
@section('title', 'Marketing Tasks')
@section('page_title', 'Tasks')
@section('header_actions')
@include('admin.partials.product-switcher')
@endsection
@section('content')
@if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="grid gap-6 lg:grid-cols-3">
@can('tasks.manage')
<div class="glass-card">
<h2 class="font-bold mb-3">New task</h2>
<form method="POST" action="{{ route('admin.tasks.store') }}" class="space-y-3">@csrf
<input name="title" required placeholder="Title" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<textarea name="notes" rows="2" placeholder="Notes" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
<select name="assignee_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<option value="">Assignee</option>
@foreach($assignees as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
</select>
<select name="product_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<option value="">Product</option>
@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach
</select>
<input type="datetime-local" name="due_at" class="w-full rounded-lg border border-slate-300 px-3 py-2">
<select name="priority" class="w-full rounded-lg border border-slate-300 px-3 py-2">
@foreach(['low','normal','high','urgent'] as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach
</select>
<button class="btn-primary">Create</button>
</form>
</div>
@endcan
<div class="lg:col-span-2 space-y-3">
@forelse($tasks as $task)
<div class="glass-card flex flex-wrap items-start justify-between gap-3">
<div>
<p class="font-semibold text-slate-900">{{ $task->title }}</p>
<p class="text-sm text-slate-600">{{ $task->status }} · {{ $task->priority }} · {{ $task->assignee?->name ?? 'Unassigned' }}</p>
<p class="text-xs text-slate-500 mt-1">Due {{ optional($task->due_at)->diffForHumans() ?? '—' }}</p>
</div>
@can('tasks.manage')
<form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="flex gap-2 items-center">@csrf
<select name="status" class="rounded-lg border border-slate-300 px-2 py-1 text-sm">
@foreach(['open','in_progress','done','cancelled'] as $s)
<option value="{{ $s }}" @selected($task->status===$s)>{{ $s }}</option>
@endforeach
</select>
<button class="btn-ghost">Save</button>
</form>
<form method="POST" action="{{ route('admin.tasks.destroy', $task) }}">@csrf<button class="btn-ghost text-red-600" onclick="return confirm('Delete?')">Delete</button></form>
@endcan
</div>
@empty
<div class="glass-card text-center py-10 text-slate-600">No tasks yet.</div>
@endforelse
{{ $tasks->links() }}
</div>
</div>
@endsection
