@extends('layouts.admin')
@section('title', 'Social Accounts')
@section('page_title', 'Social Accounts')
@section('header_actions')
    @include('admin.partials.product-switcher')
    <a href="{{ route('admin.social-accounts.create') }}" class="btn-primary">Add account</a>
@endsection
@section('content')

<div class="glass-card overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Platform</th><th class="px-4 py-3">Product</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Last posted</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
<tbody class="divide-y divide-slate-100">
@forelse($accounts as $account)
<tr>
<td class="px-4 py-3 font-semibold"><a href="{{ route('admin.social-accounts.show', $account) }}" class="hover:underline">{{ $account->name }}</a></td>
<td class="px-4 py-3">{{ $account->platform }}</td>
<td class="px-4 py-3">{{ $account->product?->name ?? '—' }}</td>
<td class="px-4 py-3">{{ $account->status }}</td>
<td class="px-4 py-3">{{ optional($account->last_posted_at)->diffForHumans() ?? 'Never' }}</td>
<td class="px-4 py-3 text-right"><a href="{{ route('admin.social-accounts.edit', $account) }}" class="btn-ghost">Edit</a></td>
</tr>
@empty
<tr><td colspan="6" class="px-4 py-8 text-center text-slate-600">No social accounts.</td></tr>
@endforelse
</tbody>
</table>
{{ $accounts->links() }}
</div>
@endsection
