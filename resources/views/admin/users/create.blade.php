@extends('layouts.admin')

@section('title', 'Admin - New User')
@section('page_title', 'New User')
@section('page_eyebrow', 'Admin Accounts')
@section('page_description', 'Create a new admin user account with access to the dashboard')

@section('content')
    <div class="max-w-4xl">
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="admin-form-card">
            <form action="{{ route('admin.users.store') }}" method="POST" class="admin-form-section">
                @csrf

                <div class="admin-form-section-title">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    User Information
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="admin-form-input"
                           placeholder="John Doe"
                           required>
                    @error('name')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="admin-form-input"
                           placeholder="user@example.com"
                           required>
                    <p class="admin-form-help">This will be used for login and notifications</p>
                    @error('email')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label admin-form-label-required">Role</label>
                    <select name="role" class="admin-form-select" required>
                        <option value="">Select a role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin - Full access to all features</option>
                        <option value="hr_manager" {{ old('role') === 'hr_manager' ? 'selected' : '' }}>HR Manager - Careers & Applications only</option>
                    </select>
                    <p class="admin-form-help">HR Managers can only access Positions and Applications</p>
                    @error('role')
                        <p class="admin-form-error">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="admin-form-divider"></div>

                <div class="admin-form-section-title">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security Settings
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="admin-form-group">
                        <label class="admin-form-label admin-form-label-required">Password</label>
                        <input type="password" name="password"
                               class="admin-form-input"
                               placeholder="••••••••"
                               required>
                        <p class="admin-form-help">Minimum 8 characters</p>
                        @error('password')
                            <p class="admin-form-error">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label admin-form-label-required">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="admin-form-input"
                               placeholder="••••••••"
                               required>
                        <p class="admin-form-help">Re-enter the password to confirm</p>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Create User Account
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
