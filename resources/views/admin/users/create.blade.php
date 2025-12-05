@extends('layouts.admin')

@section('title', 'Admin - New User')
@section('page_title', 'New User')

@section('content')
    <div class="max-w-3xl">
        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Full name"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="user@example.com"
                       required>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password *</label>
                    <input type="password" name="password"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Minimum 8 characters"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm md:text-base focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Re-enter password"
                           required>
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 rounded-lg bg-indigo-600 text-white text-sm md:text-base font-semibold hover:bg-indigo-700 transition">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:underline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection


