@extends('layouts.admin')

@section('title', 'Admin - Enquiries')
@section('page_title', 'Enquiries')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Enquiries</h2>
            <p class="text-sm text-slate-500">Contact form submissions from the website.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.enquiries.index', ['export' => 'csv']) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50">
                Export CSV
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-slate-700">Filter:</span>
            <a href="{{ route('admin.enquiries.index', ['status' => 'all'] + request()->except('status')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status', 'all') === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All ({{ $statusCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.enquiries.index', ['status' => 'new'] + request()->except('status')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                New ({{ $statusCounts['new'] ?? 0 }})
            </a>
            <a href="{{ route('admin.enquiries.index', ['status' => 'read'] + request()->except('status')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'read' ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Read ({{ $statusCounts['read'] ?? 0 }})
            </a>
            <a href="{{ route('admin.enquiries.index', ['status' => 'responded'] + request()->except('status')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'responded' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Responded ({{ $statusCounts['responded'] ?? 0 }})
            </a>
            <a href="{{ route('admin.enquiries.index', ['status' => 'archived'] + request()->except('status')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'archived' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Archived ({{ $statusCounts['archived'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by name, email, subject..." 
                       class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            @if($topics && $topics->count() > 0)
                <div>
                    <select name="topic" class="rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Topics</option>
                        @foreach($topics as $topic)
                            <option value="{{ $topic }}" {{ request('topic') === $topic ? 'selected' : '' }}>{{ $topic }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="btn-primary">Search</button>
            @if(request()->hasAny(['search', 'topic', 'status']))
                <a href="{{ route('admin.enquiries.index') }}" class="btn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-base">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Name</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Email</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Subject</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Status</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Received</th>
                    <th class="px-4 py-4 text-right font-bold text-slate-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($messages as $message)
                    <tr class="hover:bg-slate-50 {{ $message->isUnread() ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($message->isUnread())
                                    <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                @endif
                                <div>
                                    <p class="font-semibold text-base text-slate-900">{{ $message->name }}</p>
                                    @if($message->company)
                                        <p class="text-sm text-slate-600 mt-1">{{ $message->company }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $message->email }}" class="text-base font-medium text-indigo-700 hover:underline">
                                {{ $message->email }}
                            </a>
                            @if($message->phone)
                                <p class="text-sm text-slate-600 mt-1">{{ $message->phone }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-base text-slate-800">{{ $message->subject }}</p>
                            @if($message->topic)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium mt-1">
                                    {{ $message->topic }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'new' => 'bg-blue-50 text-blue-700',
                                    'read' => 'bg-slate-50 text-slate-700',
                                    'responded' => 'bg-emerald-50 text-emerald-700',
                                    'archived' => 'bg-amber-50 text-amber-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$message->status] ?? 'bg-slate-50 text-slate-700' }}">
                                {{ ucfirst($message->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-base text-slate-700">
                            {{ $message->created_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.enquiries.show', $message) }}" 
                               class="inline-flex items-center px-4 py-2 rounded-lg border-2 border-slate-300 text-slate-700 hover:bg-slate-100 hover:border-slate-400 text-sm font-medium transition">
                                View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-base text-slate-700">
                            No enquiries found. @if(request()->hasAny(['search', 'topic', 'status']))<a href="{{ route('admin.enquiries.index') }}" class="text-indigo-600 hover:underline">Clear filters</a>@endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($messages, 'links'))
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
@endsection
