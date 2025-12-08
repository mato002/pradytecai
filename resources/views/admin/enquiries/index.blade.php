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
        <table class="min-w-full divide-y divide-slate-200 text-base">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Name</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Email</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Subject</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Topic</th>
                    <th class="px-4 py-4 text-left font-bold text-slate-900">Received</th>
                    <th class="px-4 py-4 text-right font-bold text-slate-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($messages as $message)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-base text-slate-900">{{ $message->name }}</p>
                            @if($message->company)
                                <p class="text-sm text-slate-600 mt-1">{{ $message->company }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $message->email }}" class="text-base font-medium text-indigo-700 hover:underline">
                                {{ $message->email }}
                            </a>
                            @if($message->phone)
                                <p class="text-sm text-slate-600 mt-1">{{ $message->phone }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-base text-slate-800">{{ $message->subject }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 text-sm font-medium">
                                {{ $message->topic ?: 'General' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-base text-slate-700">
                            {{ $message->created_at?->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button
                                type="button"
                                class="inline-flex items-center px-4 py-2 rounded-lg border-2 border-slate-300 text-slate-700 hover:bg-slate-100 hover:border-slate-400 text-sm font-medium transition"
                                onclick="alert(@json($message->message))"
                            >
                                View message
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-base text-slate-700">
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
