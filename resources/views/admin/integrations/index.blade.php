@extends('layouts.admin')
@section('title', 'Integrations')
@section('page_title', 'Integrations')
@section('content')
@if(session('success'))<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="grid gap-6 lg:grid-cols-2">
    <div class="glass-card">
        <h2 class="text-lg font-bold mb-4">Connected</h2>
        <div class="space-y-4">
            @forelse($integrations as $integration)
                <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $integration->provider }}</p>
                        <p class="text-sm text-slate-600">{{ $integration->external_account_name ?: '—' }} · {{ $integration->status }}</p>
                        @if($integration->last_error)<p class="text-xs text-red-600 mt-1">{{ $integration->last_error }}</p>@endif
                    </div>
                    <div class="flex gap-2">
                        @can('integrations.manage')
                        <form method="POST" action="{{ route('admin.integrations.reconnect', $integration) }}">@csrf<button class="btn-ghost">Reconnect</button></form>
                        <form method="POST" action="{{ route('admin.integrations.destroy', $integration) }}">@csrf<button class="btn-ghost text-red-600" onclick="return confirm('Remove?')">Remove</button></form>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-slate-600">No integrations yet.</p>
            @endforelse
        </div>
    </div>
    @can('integrations.manage')
    <div class="glass-card">
        <h2 class="text-lg font-bold mb-4">Manual connect</h2>
        <form method="POST" action="{{ route('admin.integrations.store') }}" class="space-y-3">@csrf
            <input name="provider" required placeholder="Provider (buffer, ga4, meta…)" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <input name="external_account_name" placeholder="External account name" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                @foreach(['connected','disconnected','error','expired'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn-primary" type="submit">Save</button>
        </form>
    </div>
    @endcan
</div>
@endsection
