@extends('layouts.admin')

@section('title', 'Admin - Enquiry Details')
@section('page_title', 'Enquiry Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.enquiries.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900 mb-4">
            ← Back to Enquiries
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

    <!-- Enquiry Information Header -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $enquiry->subject }}</h2>
                <div class="flex items-center gap-3">
                    @php
                        $statusColors = [
                            'new' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'read' => 'bg-slate-50 text-slate-700 border-slate-200',
                            'responded' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'archived' => 'bg-amber-50 text-amber-700 border-amber-200',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusColors[$enquiry->status] ?? 'bg-slate-50 text-slate-700' }}">
                        {{ ucfirst($enquiry->status) }}
                    </span>
                    @if($enquiry->topic)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium">
                            {{ $enquiry->topic }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right text-sm text-slate-600">
                <p>Received: {{ $enquiry->created_at->format('M j, Y g:i A') }}</p>
                @if($enquiry->read_at)
                    <p class="mt-1">Read: {{ $enquiry->read_at->diffForHumans() }}</p>
                @endif
                @if($enquiry->responded_at)
                    <p class="mt-1">Responded: {{ $enquiry->responded_at->diffForHumans() }}</p>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 pt-6 border-t border-slate-200">
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Name</span>
                <span class="text-base font-semibold text-slate-900">{{ $enquiry->name }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Email</span>
                <a href="mailto:{{ $enquiry->email }}" class="text-base text-indigo-600 hover:underline">{{ $enquiry->email }}</a>
            </div>
            @if($enquiry->company)
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Company</span>
                    <span class="text-base text-slate-900">{{ $enquiry->company }}</span>
                </div>
            @endif
            @if($enquiry->phone)
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Phone</span>
                    <a href="tel:{{ $enquiry->phone }}" class="text-base text-slate-900">{{ $enquiry->phone }}</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Tabbed Interface -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="switchTab('message')" id="tab-message" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
                    Message
                </button>
                <button onclick="switchTab('actions')" id="tab-actions" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Actions
                </button>
                <button onclick="switchTab('notes')" id="tab-notes" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                    Admin Notes
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Message Tab -->
            <div id="content-message" class="tab-content">
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Message Content</h3>
                    <div class="bg-slate-50 rounded-lg p-6 border border-slate-200">
                        <p class="text-base text-slate-700 whitespace-pre-wrap">{{ $enquiry->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions Tab -->
            <div id="content-actions" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Update Status -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Update Status</h3>
                        <form method="POST" action="{{ route('admin.enquiries.updateStatus', $enquiry) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                                <select name="status" id="status" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="new" {{ $enquiry->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="read" {{ $enquiry->status === 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="responded" {{ $enquiry->status === 'responded' ? 'selected' : '' }}>Responded</option>
                                    <option value="archived" {{ $enquiry->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primary">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Reply to Enquiry -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Reply to Enquiry</h3>
                        <form method="POST" action="{{ route('admin.enquiries.reply', $enquiry) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="reply_subject" class="block text-sm font-medium text-slate-700 mb-2">Subject</label>
                                <input type="text" name="reply_subject" id="reply_subject" 
                                       value="Re: {{ $enquiry->subject }}"
                                       class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="reply_message" class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                                <textarea name="reply_message" id="reply_message" rows="8" 
                                          class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                            </div>
                            <button type="submit" class="btn-primary">
                                Send Reply
                            </button>
                        </form>
                    </div>

                    <!-- Delete Enquiry -->
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4 text-red-600">Danger Zone</h3>
                        <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <p class="text-sm text-slate-600 mb-4">Once deleted, this enquiry cannot be recovered.</p>
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                                Delete Enquiry
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notes Tab -->
            <div id="content-notes" class="tab-content hidden">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Admin Notes</h3>
                    <form method="POST" action="{{ route('admin.enquiries.updateStatus', $enquiry) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="{{ $enquiry->status }}">
                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="10" 
                                      class="w-full rounded-lg border border-slate-300 px-4 py-2 text-base focus:border-indigo-500 focus:ring-indigo-500">{{ $enquiry->admin_notes }}</textarea>
                            <p class="mt-2 text-sm text-slate-500">Add internal notes about this enquiry. These notes are only visible to admins.</p>
                        </div>
                        <button type="submit" class="btn-primary">
                            Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-slate-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('active', 'border-indigo-600', 'text-indigo-600');
            activeButton.classList.remove('border-transparent', 'text-slate-500');
        }
    </script>
@endsection


