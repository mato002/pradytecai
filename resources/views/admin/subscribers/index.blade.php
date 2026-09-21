@extends('layouts.admin')
@section('title', 'Subscribers')
@section('page_title', 'Newsletter Subscribers')
@section('content')
<div class="mb-4 glass-card">
<form method="GET" class="flex flex-wrap gap-3">
<input name="search" value="{{ request('search') }}" placeholder="Email search" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
<option value="all">All</option>
<option value="subscribed" @selected(request('status')==='subscribed')>Subscribed</option>
<option value="unsubscribed" @selected(request('status')==='unsubscribed')>Unsubscribed</option>
</select>
<button class="btn-primary">Filter</button>
</form>
</div>
<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm"><thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Email</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Interest</th><th class="px-4 py-3">Subscribed</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($subscribers as $subscriber)
<tr>
<td class="px-4 py-3">{{ $subscriber->email }}</td>
<td class="px-4 py-3">{{ $subscriber->status }}</td>
<td class="px-4 py-3">{{ $subscriber->product_interest ?? '—' }}</td>
<td class="px-4 py-3">{{ optional($subscriber->subscribed_at)->format('M j, Y') ?? '—' }}</td>
</tr>
@empty
<tr><td colspan="4" class="px-4 py-8 text-center text-slate-600">No subscribers stored yet.</td></tr>
@endforelse
</tbody></table>
{{ $subscribers->links() }}
</div>
@endsection
