@extends('layouts.admin')

@section('title', 'Admin - Job Applications')
@section('page_title', 'Job Applications')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Job Applications</h2>
            <p class="text-sm text-slate-500">Review and manage job applications from candidates.</p>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-2 flex flex-wrap gap-2">
        <a href="{{ route('admin.applications.index', ['status' => 'all']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'all' || !request('status') ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            All ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'pending' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Pending ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'reviewing']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'reviewing' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Reviewing ({{ $statusCounts['reviewing'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'shortlisted']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'shortlisted' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Shortlisted ({{ $statusCounts['shortlisted'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'interviewed']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'interviewed' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Interviewed ({{ $statusCounts['interviewed'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'accepted']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'accepted' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Accepted ({{ $statusCounts['accepted'] }})
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') == 'rejected' ? 'bg-indigo-600 text-white' : 'text-slate-700 hover:bg-slate-50' }}">
            Rejected ({{ $statusCounts['rejected'] }})
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @if($applications->isEmpty())
            <div class="p-12 text-center">
                <p class="text-slate-600">No applications found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Candidate</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Position</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Applied</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($applications as $application)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $application->name }}</div>
                                <div class="text-sm text-slate-500">{{ $application->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $application->position->title }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700',
                                        'reviewing' => 'bg-blue-50 text-blue-700',
                                        'shortlisted' => 'bg-purple-50 text-purple-700',
                                        'interviewed' => 'bg-indigo-50 text-indigo-700',
                                        'accepted' => 'bg-emerald-50 text-emerald-700',
                                        'rejected' => 'bg-red-50 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$application->status] ?? 'bg-slate-50 text-slate-700' }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $application->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.applications.show', $application) }}" 
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-4 py-3 border-t border-slate-200">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
@endsection


