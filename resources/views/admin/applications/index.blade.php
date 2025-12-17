@extends('layouts.admin')

@section('title', 'Admin - Job Applications')
@section('page_title', 'Job Applications')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Job Applications</h2>
            <p class="text-sm text-slate-500">Review and manage job applications from candidates.</p>
        </div>
        <a href="{{ route('admin.applications.index', ['export' => 'csv'] + request()->except('export')) }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50">
            Export CSV
        </a>
    </div>

    <!-- Statistics Dashboard -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Applications</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-slate-600">This month: {{ $stats['this_month'] }}</span>
                @if($stats['last_month'] > 0)
                    @php
                        $change = $stats['this_month'] > $stats['last_month'] 
                            ? round((($stats['this_month'] - $stats['last_month']) / $stats['last_month']) * 100, 1)
                            : round((($stats['last_month'] - $stats['this_month']) / $stats['last_month']) * 100, 1);
                        $isIncrease = $stats['this_month'] > $stats['last_month'];
                    @endphp
                    <span class="ml-2 {{ $isIncrease ? 'text-emerald-600' : 'text-red-600' }}">
                        ({{ $isIncrease ? '+' : '-' }}{{ $change }}%)
                    </span>
                @endif
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending Review</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['pending_review'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-600">
                Needs attention
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Acceptance Rate</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['acceptance_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-600">
                {{ $stats['accepted_count'] }} accepted / {{ $stats['accepted_count'] + $stats['rejected_count'] }} processed
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Avg Rating</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">
                        {{ $stats['avg_rating'] ? number_format($stats['avg_rating'], 1) : '—' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-600">
                Out of 5.0
            </div>
        </div>
    </div>

    <!-- Top Positions -->
    @if($stats['by_position']->count() > 0)
        <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Top Positions by Applications</h3>
            <div class="space-y-2">
                @foreach($stats['by_position'] as $positionStat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-700">{{ $positionStat->position->title ?? 'Unknown' }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-slate-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($positionStat->count / $stats['total']) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-900 w-8 text-right">{{ $positionStat->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" action="{{ route('admin.applications.index') }}" class="space-y-4">
            <div class="grid md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, email, position..."
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Position Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Position</label>
                    <select name="position_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                {{ $position->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Rating</label>
                    <select name="rating" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.applications.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
                        Clear
                    </a>
                </div>
            </div>

            <!-- Preserve status filter -->
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>
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

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @if($applications->isEmpty())
            <div class="p-12 text-center">
                <p class="text-slate-600">No applications found.</p>
            </div>
        @else
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions-bar" class="hidden px-4 py-3 bg-indigo-50 border-b border-indigo-200 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span id="selected-count" class="text-sm font-medium text-indigo-900">0 selected</span>
                    <div class="flex items-center gap-2">
                        <select id="bulk-action" class="px-3 py-1.5 border border-indigo-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Choose action...</option>
                            <option value="status:pending">Set Status: Pending</option>
                            <option value="status:reviewing">Set Status: Reviewing</option>
                            <option value="status:shortlisted">Set Status: Shortlisted</option>
                            <option value="status:interviewed">Set Status: Interviewed</option>
                            <option value="status:accepted">Set Status: Accepted</option>
                            <option value="status:rejected">Set Status: Rejected</option>
                            <option value="delete">Delete Selected</option>
                            <option value="export">Export Selected</option>
                        </select>
                        <button onclick="executeBulkAction()" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                            Apply
                        </button>
                        <button onclick="clearSelection()" class="px-4 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <form id="bulk-action-form" method="POST" action="{{ route('admin.applications.bulkAction') }}">
                @csrf
                <input type="hidden" name="action" id="bulk-action-input">
                <input type="hidden" name="ids" id="bulk-ids-input">
                
                <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="select-all" onclick="toggleSelectAll()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Candidate</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Position</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Rating</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Applied</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($applications as $application)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="application_ids[]" value="{{ $application->id }}" 
                                       class="application-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                       onchange="updateBulkActionsBar()">
                            </td>
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
                            <td class="px-4 py-3">
                                @if($application->rating)
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $application->rating ? 'text-yellow-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
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
            </form>
        @endif
    </div>

    @push('scripts')
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.application-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActionsBar();
        }

        function updateBulkActionsBar() {
            const checkboxes = document.querySelectorAll('.application-checkbox:checked');
            const bulkBar = document.getElementById('bulk-actions-bar');
            const selectedCount = document.getElementById('selected-count');
            
            if (checkboxes.length > 0) {
                bulkBar.classList.remove('hidden');
                selectedCount.textContent = `${checkboxes.length} selected`;
            } else {
                bulkBar.classList.add('hidden');
            }
            
            // Update select-all checkbox state
            const allCheckboxes = document.querySelectorAll('.application-checkbox');
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            document.getElementById('select-all').checked = allChecked && allCheckboxes.length > 0;
        }

        function clearSelection() {
            document.querySelectorAll('.application-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('select-all').checked = false;
            updateBulkActionsBar();
        }

        function executeBulkAction() {
            const action = document.getElementById('bulk-action').value;
            if (!action) {
                alert('Please select an action');
                return;
            }

            const checkboxes = document.querySelectorAll('.application-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one application');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            
            if (action === 'delete' && !confirm(`Are you sure you want to delete ${ids.length} application(s)? This action cannot be undone.`)) {
                return;
            }

            if (action === 'export') {
                // Export selected applications
                const params = new URLSearchParams();
                params.append('export', 'csv');
                params.append('ids', ids.join(','));
                window.location.href = '{{ route("admin.applications.index") }}?' + params.toString();
                return;
            }

            // Set form values and submit
            document.getElementById('bulk-action-input').value = action;
            document.getElementById('bulk-ids-input').value = ids.join(',');
            document.getElementById('bulk-action-form').submit();
        }
    </script>
    @endpush
@endsection


