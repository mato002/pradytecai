@extends('layouts.admin')

@section('title', 'Campaigns - Pradytecai')
@section('page_title', 'Campaigns')
@section('page_eyebrow', 'Marketing')

@section('content')
@can('campaigns.create')
    <a href="{{ route('admin.campaigns.create') }}" class="btn-primary mb-4 inline-flex">New campaign</a>
@endcan
<div class="glass-card overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500">
                <th class="py-2">Name</th><th>Status</th><th>Products</th><th>Owner</th><th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($campaigns as $c)
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold">
                        <a href="{{ route('admin.campaigns.show', $c) }}" class="text-indigo-700">{{ $c->name }}</a>
                    </td>
                    <td>{{ $c->status }}</td>
                    <td>{{ $c->products->pluck('name')->join(', ') }}</td>
                    <td>{{ $c->owner?->name ?? '—' }}</td>
                    <td><a href="{{ route('admin.campaigns.edit', $c) }}" class="text-indigo-600">Edit</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-6 text-slate-600">No campaigns yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $campaigns->links() }}
</div>
@endsection
