@extends('layouts.admin')

@section('title', 'Admin - Leads')
@section('page_title', 'Leads')
@section('header_actions')
    @include('admin.partials.product-switcher')
@endsection

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Leads</h2>
            <p class="text-sm text-slate-500">Inbound contact and demo pipeline.</p>
        </div>
        @can('leads.export')
        <a href="{{ route('admin.enquiries.index', ['export' => 'csv'] + request()->except('export')) }}" class="btn-ghost">Export CSV</a>
        @endcan
    </div>

    <div class="mb-6 glass-card">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.enquiries.index', ['status' => 'all'] + request()->except('status')) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium {{ request('status', 'all') === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                All ({{ $statusCounts['all'] ?? 0 }})
            </a>
            @foreach(($leadStatuses ?? []) as $status)
                <a href="{{ route('admin.enquiries.index', ['status' => $status] + request()->except('status')) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium {{ request('status') === $status ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ str_replace('_', ' ', $status) }} ({{ $statusCounts[$status] ?? 0 }})
                </a>
            @endforeach
        </div>
    </div>

    <div class="mb-6 glass-card">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="grid md:grid-cols-4 gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, subject..."
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm md:col-span-2">
            <select name="assigned_to" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All assignees</option>
                @foreach(($assignees ?? []) as $assignee)
                    <option value="{{ $assignee->id }}" @selected(request('assigned_to') == $assignee->id)>{{ $assignee->name }}</option>
                @endforeach
            </select>
            <select name="product_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All products</option>
                @foreach(($products ?? []) as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <input type="date" name="next_follow_up_at" value="{{ request('next_follow_up_at') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="awaiting_follow_up" value="1" @checked(request()->boolean('awaiting_follow_up'))>
                Awaiting follow-up
            </label>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('admin.enquiries.index') }}" class="btn-ghost">Clear</a>
            </div>
        </form>
    </div>

    <div class="glass-card overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Assignee</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Follow-up</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($messages as $message)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $message->name }}</td>
                        <td class="px-4 py-3">{{ $message->email }}</td>
                        <td class="px-4 py-3">{{ str_replace('_', ' ', $message->status) }}</td>
                        <td class="px-4 py-3">{{ $message->assignee?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ optional($message->next_follow_up_at)->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.enquiries.show', $message) }}" class="btn-ghost">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-600">No leads found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $messages->links() }}</div>
    </div>
@endsection
