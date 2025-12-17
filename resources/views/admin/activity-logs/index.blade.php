@extends('layouts.admin')

@section('title', 'Admin - Activity Logs')
@section('page_title', 'Activity Logs')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Activity Logs</h2>
            <p class="text-sm text-slate-500">Track all admin actions and changes.</p>
        </div>
    </div>

    <!-- Action Filter Tabs -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-slate-700">Filter:</span>
            <a href="{{ route('admin.activity-logs.index', ['action' => 'all'] + request()->except('action')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('action', 'all') === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All ({{ $actionCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('admin.activity-logs.index', ['action' => 'created'] + request()->except('action')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('action') === 'created' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Created ({{ $actionCounts['created'] ?? 0 }})
            </a>
            <a href="{{ route('admin.activity-logs.index', ['action' => 'updated'] + request()->except('action')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('action') === 'updated' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Updated ({{ $actionCounts['updated'] ?? 0 }})
            </a>
            <a href="{{ route('admin.activity-logs.index', ['action' => 'deleted'] + request()->except('action')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('action') === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Deleted ({{ $actionCounts['deleted'] ?? 0 }})
            </a>
            <a href="{{ route('admin.activity-logs.index', ['action' => 'viewed'] + request()->except('action')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('action') === 'viewed' ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                Viewed ({{ $actionCounts['viewed'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search activity logs..." 
                       class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            @if($modelTypes && $modelTypes->count() > 0)
                <div>
                    <select name="model_type" class="rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Models</option>
                        @foreach($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') === $modelType ? 'selected' : '' }}>{{ $modelType }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="btn-primary">Search</button>
            @if(request()->hasAny(['search', 'model_type', 'action']))
                <a href="{{ route('admin.activity-logs.index') }}" class="btn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="divide-y divide-slate-200">
            @forelse($logs as $log)
                <div class="p-6 hover:bg-slate-50">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-emerald-100 text-emerald-700',
                                        'updated' => 'bg-blue-100 text-blue-700',
                                        'deleted' => 'bg-red-100 text-red-700',
                                        'viewed' => 'bg-slate-100 text-slate-700',
                                        'replied' => 'bg-indigo-100 text-indigo-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $actionColors[$log->action] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                                @if($log->model_type)
                                    <span class="text-xs text-slate-500">
                                        {{ class_basename($log->model_type) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-base font-medium text-slate-900 mb-1">
                                {{ $log->description ?? 'No description' }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-slate-600 mt-2">
                                @if($log->user)
                                    <span>By: <strong>{{ $log->user->name }}</strong></span>
                                @endif
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                @if($log->ip_address)
                                    <span>IP: {{ $log->ip_address }}</span>
                                @endif
                            </div>
                            @if($log->changes && is_array($log->changes))
                                <div class="mt-3 p-3 bg-slate-50 rounded-lg text-sm">
                                    <details>
                                        <summary class="cursor-pointer font-medium text-slate-700 hover:text-slate-900">View Changes</summary>
                                        <div class="mt-2 space-y-2">
                                            @if(isset($log->changes['old']) && isset($log->changes['new']))
                                                @foreach($log->changes['old'] as $key => $oldValue)
                                                    @if(isset($log->changes['new'][$key]) && $log->changes['new'][$key] != $oldValue)
                                                        <div class="flex gap-2">
                                                            <span class="font-medium text-slate-700">{{ $key }}:</span>
                                                            <span class="text-red-600 line-through">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                                            <span>→</span>
                                                            <span class="text-emerald-600">{{ is_array($log->changes['new'][$key]) ? json_encode($log->changes['new'][$key]) : $log->changes['new'][$key] }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </details>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-base text-slate-700">
                    No activity logs found. @if(request()->hasAny(['search', 'model_type', 'action']))<a href="{{ route('admin.activity-logs.index') }}" class="text-indigo-600 hover:underline">Clear filters</a>@endif
                </div>
            @endforelse
        </div>

        @if(method_exists($logs, 'links'))
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection





