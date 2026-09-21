@extends('layouts.admin')
@section('title', $account->name)
@section('page_title', $account->name)
@section('header_actions')
<a href="{{ route('admin.social-accounts.edit', $account) }}" class="btn-primary">Edit</a>
<form method="POST" action="{{ route('admin.social-accounts.destroy', $account) }}">@csrf<button class="btn-ghost text-red-600" onclick="return confirm('Delete?')">Delete</button></form>
@endsection
@section('content')
<div class="glass-card space-y-2">
<p><span class="text-slate-500">Platform:</span> {{ $account->platform }}</p>
<p><span class="text-slate-500">Product:</span> {{ $account->product?->name ?? '—' }}</p>
<p><span class="text-slate-500">Integration:</span> {{ $account->integration?->provider ?? '—' }}</p>
<p><span class="text-slate-500">Status / token:</span> {{ $account->status }} / {{ $account->token_status }}</p>
<p><span class="text-slate-500">Last posted:</span> {{ optional($account->last_posted_at)->toDayDateTimeString() ?? 'Never' }}</p>
<p><span class="text-slate-500">Followers:</span> {{ $account->follower_count !== null ? number_format($account->follower_count) : '—' }}</p>
</div>
@endsection
