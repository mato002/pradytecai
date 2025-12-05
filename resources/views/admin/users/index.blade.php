@extends('layouts.admin')

@section('title', 'Admin - Users')
@section('page_title', 'Users')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Users</h2>
            <p class="text-sm text-slate-500">Manage admin users who can access this panel.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            <span class="mr-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </span>
            Add User
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Email</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Registered</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900">{{ $user->name }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <a href="mailto:{{ $user->email }}" class="text-indigo-600 hover:underline">
                                {{ $user->email }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-500">
                            {{ $user->created_at?->format('M j, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">
                                Active
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs md:text-sm hover:bg-slate-50 transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-300 bg-white text-red-600 text-xs md:text-sm hover:bg-red-50 transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($users, 'links'))
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
