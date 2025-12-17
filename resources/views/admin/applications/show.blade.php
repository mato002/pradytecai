@extends('layouts.admin')

@section('title', 'Admin - Application Review')
@section('page_title', 'Application Review')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900 mb-4">
            ← Back to Applications
        </a>
        <h2 class="text-xl font-semibold text-slate-900">Application Review</h2>
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

    <!-- Candidate Information Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
        <div class="grid md:grid-cols-5 gap-6">
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Name</span>
                <span class="text-base font-semibold text-slate-900">{{ $application->name }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Email</span>
                <a href="mailto:{{ $application->email }}" class="text-base text-indigo-600 hover:underline">{{ $application->email }}</a>
            </div>
            @if($application->phone)
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Phone</span>
                    <a href="tel:{{ $application->phone }}" class="text-base text-slate-900">{{ $application->phone }}</a>
                </div>
            @endif
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Position</span>
                <span class="text-base text-slate-900">{{ $application->position->title }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Applied</span>
                <span class="text-base text-slate-900">{{ $application->created_at->format('M j, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Tabbed Interface -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="switchTab('overview')" id="tab-overview" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
                    Overview
                </button>
                <button onclick="switchTab('cover-letter')" id="tab-cover-letter" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Cover Letter
                </button>
                <button onclick="switchTab('actions')" id="tab-actions" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Actions
                </button>
                <button onclick="switchTab('communication')" id="tab-communication" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Communication
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    History
                </button>
                <button onclick="switchTab('comments')" id="tab-comments" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Comments
                    @if($comments && $comments->count() > 0)
                        <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs">{{ $comments->count() }}</span>
                    @endif
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Application Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Status</dt>
                                <dd>
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
                                </dd>
                            </div>
                            @if($application->rating)
                                <div>
                                    <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Rating</dt>
                                    <dd class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $application->rating ? 'text-yellow-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-sm text-slate-700">({{ $application->rating }}/5)</span>
                                    </dd>
                                </div>
                            @endif
                            @if($application->interview_scheduled_at)
                                <div>
                                    <dt class="text-xs text-slate-500 uppercase tracking-wide mb-1">Interview Scheduled</dt>
                                    <dd class="text-sm text-slate-900">{{ $application->interview_scheduled_at->format('F j, Y \a\t g:i A') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-4">Documents</h3>
                        <div class="space-y-3">
                            @if($application->resume_path)
                                <a href="{{ route('admin.applications.downloadResume', $application) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Resume
                                </a>
                            @else
                                <p class="text-sm text-slate-500">No resume uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>
                @if($application->admin_notes)
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Admin Notes</h3>
                        <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-700 whitespace-pre-wrap">
                            {{ $application->admin_notes }}
                        </div>
                    </div>
                @endif
                @if($application->interview_notes)
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Interview Notes</h3>
                        <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-700 whitespace-pre-wrap">
                            {{ $application->interview_notes }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Cover Letter Tab -->
            <div id="content-cover-letter" class="tab-content hidden">
                <div class="prose max-w-none text-sm md:text-base text-slate-700 whitespace-pre-wrap leading-relaxed">
                    {{ $application->cover_letter }}
                </div>
            </div>

            <!-- Actions Tab -->
            <div id="content-actions" class="tab-content hidden">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Update Status -->
                    <div class="bg-slate-50 rounded-lg p-6">
                        <h3 class="text-base font-semibold text-slate-900 mb-4">Update Status</h3>
                        <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="reviewing" {{ $application->status == 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                                    <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                    <option value="interviewed" {{ $application->status == 'interviewed' ? 'selected' : '' }}>Interviewed</option>
                                    <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Rating (1-5)</label>
                                <input type="number" name="rating" min="1" max="5" value="{{ $application->rating }}" 
                                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Admin Notes</label>
                                <textarea name="admin_notes" rows="4" 
                                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $application->admin_notes }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Schedule Interview -->
                    @if(in_array($application->status, ['reviewing', 'shortlisted', 'interviewed']))
                        <div class="bg-slate-50 rounded-lg p-6">
                            <h3 class="text-base font-semibold text-slate-900 mb-4">Schedule Interview</h3>
                            
                            @if($application->interview_scheduled_at)
                                <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <p class="text-sm font-medium text-indigo-900">Currently Scheduled</p>
                                    <p class="text-sm text-indigo-700 mt-1">
                                        {{ $application->interview_scheduled_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            @endif

                            <form action="{{ route('admin.applications.scheduleInterview', $application) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Interview Date & Time</label>
                                    <input type="datetime-local" name="interview_scheduled_at" 
                                           value="{{ $application->interview_scheduled_at ? $application->interview_scheduled_at->format('Y-m-d\TH:i') : '' }}"
                                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Interview Notes</label>
                                    <textarea name="interview_notes" rows="4" 
                                              class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $application->interview_notes }}</textarea>
                                </div>

                                <button type="submit" class="w-full bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-sky-700 transition">
                                    {{ $application->interview_scheduled_at ? 'Update Interview' : 'Schedule Interview' }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-lg p-6">
                            <h3 class="text-base font-semibold text-slate-900 mb-4">Schedule Interview</h3>
                            <div class="p-4 bg-slate-100 border border-slate-300 rounded-lg">
                                <p class="text-sm text-slate-600">
                                    @if($application->status === 'rejected')
                                        Interview scheduling is not available for rejected applications.
                                    @elseif($application->status === 'accepted')
                                        Interview scheduling is not available for accepted applications.
                                    @elseif($application->status === 'pending')
                                        Please move the application to "Reviewing" or "Shortlisted" status before scheduling an interview.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Communication Tab -->
            <div id="content-communication" class="tab-content hidden">
                <div class="max-w-2xl">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">Send Message to Candidate</h3>
                    <form action="{{ route('admin.applications.sendMessage', $application) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Communication Channels</label>
                            <div class="grid md:grid-cols-3 gap-3">
                                <label class="flex items-center p-3 border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer">
                                    <input type="checkbox" name="channels[]" value="email" checked
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm text-slate-700">Email</span>
                                </label>
                                @if($application->phone)
                                    <label class="flex items-center p-3 border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" name="channels[]" value="sms"
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm text-slate-700">SMS</span>
                                    </label>
                                    <label class="flex items-center p-3 border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer">
                                        <input type="checkbox" name="channels[]" value="whatsapp"
                                               class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3 text-sm text-slate-700">WhatsApp</span>
                                    </label>
                                @else
                                    <div class="col-span-2 text-sm text-slate-500 p-3">
                                        SMS/WhatsApp unavailable (no phone number)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Subject (Email only)</label>
                            <input type="text" name="subject" 
                                   value="Re: Your Application - {{ $application->position->title }}"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Message *</label>
                            <textarea name="message" rows="6" required
                                      placeholder="Type your message to the candidate here..."
                                      class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            <p class="mt-1 text-xs text-slate-500">Minimum 10 characters required</p>
                        </div>

                        <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- History Tab -->
            <div id="content-history" class="tab-content hidden">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Activity Timeline</h3>
                @if($activityLogs && $activityLogs->count() > 0)
                    <div class="space-y-4">
                        @foreach($activityLogs as $log)
                            <div class="flex gap-4 pb-4 border-b border-slate-200 last:border-0">
                                <div class="flex-shrink-0">
                                    @php
                                        $actionIcons = [
                                            'created' => 'M12 4v16m8-8H4',
                                            'updated' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                                            'viewed' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                            'interview_scheduled' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                            'message_sent' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                        ];
                                        $actionColors = [
                                            'created' => 'bg-emerald-100 text-emerald-700',
                                            'updated' => 'bg-blue-100 text-blue-700',
                                            'viewed' => 'bg-slate-100 text-slate-700',
                                            'interview_scheduled' => 'bg-purple-100 text-purple-700',
                                            'message_sent' => 'bg-indigo-100 text-indigo-700',
                                        ];
                                        $icon = $actionIcons[$log->action] ?? 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';
                                        $color = $actionColors[$log->action] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-slate-900">
                                                {{ $log->description ?? ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </p>
                                            @if($log->changes && is_array($log->changes))
                                                <div class="mt-2 text-xs text-slate-600 space-y-1">
                                                    @if(isset($log->changes['old']) && isset($log->changes['new']))
                                                        @foreach($log->changes['old'] as $key => $oldValue)
                                                            @if(isset($log->changes['new'][$key]) && $log->changes['new'][$key] != $oldValue)
                                                                <div>
                                                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                    <span class="line-through text-red-600">{{ $oldValue }}</span>
                                                                    <span>→</span>
                                                                    <span class="text-emerald-600">{{ $log->changes['new'][$key] }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 whitespace-nowrap">
                                            {{ $log->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                        @if($log->user)
                                            <span>By {{ $log->user->name }}</span>
                                        @else
                                            <span>System</span>
                                        @endif
                                        @if($log->ip_address)
                                            <span>•</span>
                                            <span>{{ $log->ip_address }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-sm text-slate-600">No activity recorded yet.</p>
                    </div>
                @endif
            </div>

            <!-- Comments Tab -->
            <div id="content-comments" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Add Comment Form -->
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-4">Add Comment</h3>
                        <form action="{{ route('admin.applications.addComment', $application) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <textarea name="comment" rows="4" required
                                          placeholder="Add a comment or note about this application..."
                                          class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_internal" value="1" 
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-slate-700">Internal note (only visible to admins)</span>
                                </label>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    @if($comments && $comments->count() > 0)
                        <div class="space-y-4">
                            <h3 class="text-base font-semibold text-slate-900">Comments & Notes</h3>
                            @foreach($comments as $comment)
                                <div class="bg-white border border-slate-200 rounded-lg p-4 {{ $comment->is_internal ? 'bg-amber-50 border-amber-200' : '' }}">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-semibold text-indigo-700">
                                                    {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-900">{{ $comment->user->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if($comment->is_internal)
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">Internal</span>
                                            @endif
                                        </div>
                                        @if($comment->user_id === auth()->id())
                                            <form action="{{ route('admin.applications.deleteComment', $comment) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                                @csrf
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="text-sm text-slate-700 whitespace-pre-wrap mb-3">
                                        {{ $comment->comment }}
                                    </div>

                                    <!-- Replies -->
                                    @if($comment->replies->count() > 0)
                                        <div class="ml-8 mt-3 space-y-3 border-l-2 border-slate-200 pl-4">
                                            @foreach($comment->replies as $reply)
                                                <div class="bg-slate-50 rounded p-3">
                                                    <div class="flex items-start justify-between mb-1">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-6 h-6 bg-slate-200 rounded-full flex items-center justify-center">
                                                                <span class="text-xs font-semibold text-slate-600">
                                                                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-medium text-slate-900">{{ $reply->user->name ?? 'Unknown' }}</p>
                                                                <p class="text-xs text-slate-500">{{ $reply->created_at->diffForHumans() }}</p>
                                                            </div>
                                                        </div>
                                                        @if($reply->user_id === auth()->id())
                                                            <form action="{{ route('admin.applications.deleteComment', $reply) }}" method="POST" 
                                                                  onsubmit="return confirm('Are you sure?')" class="inline">
                                                                @csrf
                                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-slate-700 whitespace-pre-wrap">{{ $reply->comment }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Reply Form -->
                                    <div class="mt-3">
                                        <form action="{{ route('admin.applications.addComment', $application) }}" method="POST" class="flex gap-2">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <input type="text" name="comment" placeholder="Reply..." required
                                                   class="flex-1 px-3 py-1.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <button type="submit" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-sm hover:bg-slate-200 transition">
                                                Reply
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="mt-4 text-sm text-slate-600">No comments yet. Be the first to add one!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active state from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-slate-500');
            });
            
            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.add('active', 'border-indigo-600', 'text-indigo-600');
            activeTab.classList.remove('border-transparent', 'text-slate-500');
        }
    </script>
    @endpush
@endsection
