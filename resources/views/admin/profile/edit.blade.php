@extends('layouts.admin')

@section('title', 'Edit Profile - Admin')
@section('page_title', 'Edit Profile')
@section('page_description', 'Update your account information and password')

@push('styles')
<style>
    .form-group {
        position: relative;
    }
    
    .floating-label {
        position: absolute;
        left: 1rem;
        top: 1rem;
        pointer-events: none;
        transition: all 0.2s ease;
        color: #64748b;
        font-size: 0.875rem;
    }
    
    .form-input:focus ~ .floating-label,
    .form-input:not(:placeholder-shown) ~ .floating-label {
        top: -0.5rem;
        left: 0.75rem;
        font-size: 0.75rem;
        color: #6366f1;
        background: white;
        padding: 0 0.25rem;
    }
    
    .form-input {
        transition: all 0.3s ease;
    }
    
    .form-input:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    
    .section-divider {
        position: relative;
        overflow: hidden;
    }
    
    .section-divider::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #6366f1, transparent);
        animation: slide 3s infinite;
    }
    
    @keyframes slide {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .password-strength {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
</style>
@endpush

@section('content')
    <div class="glass-card">
        @if($errors->any())
            <div class="mb-6 rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 shadow-lg">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-red-900 mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-8" id="profile-form">
            @csrf

            <!-- Personal Information Section -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Personal Information</h3>
                        <p class="text-sm text-slate-600">Update your name and email address</p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                            placeholder=" "
                            class="form-input w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                        >
                        <label for="name" class="floating-label">Full Name *</label>
                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Your display name
                        </div>
                    </div>

                    <div class="form-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            required
                            placeholder=" "
                            class="form-input w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                        >
                        <label for="email" class="floating-label">Email Address *</label>
                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Used for login and notifications
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="section-divider border-t-2 border-slate-200 pt-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Change Password</h3>
                        <p class="text-sm text-slate-600">Leave blank to keep current password</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="form-group">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            placeholder=" "
                            class="form-input w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                        >
                        <label for="current_password" class="floating-label">Current Password</label>
                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Required to change password
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder=" "
                                class="form-input w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                            >
                            <label for="password" class="floating-label">New Password</label>
                            <div class="mt-2">
                                <div class="password-strength bg-slate-200" id="password-strength"></div>
                                <p class="mt-1 text-xs text-slate-500">Minimum 8 characters</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                placeholder=" "
                                class="form-input w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"
                            >
                            <label for="password_confirmation" class="floating-label">Confirm New Password</label>
                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Must match new password
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t-2 border-slate-200">
                <a href="{{ route('admin.profile.show') }}" class="btn-ghost w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength');
        
        if (passwordInput && strengthBar) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 1;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
                if (password.match(/\d/)) strength += 1;
                if (password.match(/[^a-zA-Z\d]/)) strength += 1;
                
                strengthBar.style.width = (strength * 25) + '%';
                
                if (strength === 0) {
                    strengthBar.style.backgroundColor = '#ef4444';
                } else if (strength <= 2) {
                    strengthBar.style.backgroundColor = '#f59e0b';
                } else if (strength === 3) {
                    strengthBar.style.backgroundColor = '#3b82f6';
                } else {
                    strengthBar.style.backgroundColor = '#10b981';
                }
            });
        }
    </script>
    @endpush
@endsection
