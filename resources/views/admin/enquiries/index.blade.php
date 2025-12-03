@extends('layouts.admin')

@section('title', 'Admin - Enquiries')
@section('page_title', 'Enquiries')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Enquiries</h2>
            <p class="text-sm text-slate-500">Recent contact form submissions from the website.</p>
        </div>
        <button class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50">
            Refresh
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Subject</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Topic</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Received</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($messages as $message)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $message->name }}</p>
                            @if($message->company)
                                <p class="text-xs text-slate-500">{{ $message->company }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $message->email }}" class="text-indigo-600 hover:underline">
                                {{ $message->email }}
                            </a>
                            @if($message->phone)
                                <p class="text-xs text-slate-500">{{ $message->phone }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $message->subject }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-700 text-xs">
                                {{ $message->topic ?: 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-500">
                            {{ $message->created_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                type="button"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs md:text-sm"
                                onclick="alert(@json($message->message))"
                            >
                                View message
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">
                            No enquiries yet. Messages submitted via the contact form will appear here.
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
