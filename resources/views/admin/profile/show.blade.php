@extends('layouts.admin')

@section('title', 'My Profile - Admin')
@section('page_title', 'My Profile')
@section('page_description', 'View and manage your admin account information')

@push('styles')
<style>
    .profile-avatar {
        position: relative;
        animation: avatarPulse 2s ease-in-out infinite;
    }
    
    @keyframes avatarPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    .info-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #3b82f6, #06b6d4);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .info-card:hover::before {
        transform: scaleX(1);
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .stat-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 shadow-lg animate-slide-down">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-emerald-900">Success!</p>
                    <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="glass-card mb-6 overflow-hidden relative">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 pb-6 border-b border-slate-200">
                <div class="profile-avatar">
                    <div class="relative">
                        <div class="flex h-28 w-28 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-4xl font-bold text-white shadow-xl shadow-indigo-500/50">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-8 w-8 rounded-full bg-emerald-500 border-4 border-white shadow-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-3xl font-bold text-slate-900">{{ $user->name }}</h2>
                        <span class="stat-badge px-3 py-1 rounded-full text-xs font-semibold text-white">
                            Admin
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Member since {{ $user->created_at->format('F Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.profile.edit') }}" class="btn-primary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Account Information Card -->
        <div class="glass-card info-card">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 rounded-xl bg-indigo-100">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Account Information</h3>
            </div>
            <dl class="space-y-4">
                <div class="flex items-start justify-between py-3 border-b border-slate-100 last:border-0">
                    <div class="flex-1">
                        <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Full Name</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $user->name }}</dd>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex items-start justify-between py-3 border-b border-slate-100 last:border-0">
                    <div class="flex-1">
                        <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Email Address</dt>
                        <dd class="text-base font-medium text-slate-900 break-all">{{ $user->email }}</dd>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="flex items-start justify-between py-3 border-b border-slate-100 last:border-0">
                    <div class="flex-1">
                        <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Account Created</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $user->created_at->format('F j, Y') }}</dd>
                        <dd class="text-xs text-slate-500 mt-1">{{ $user->created_at->format('g:i A') }}</dd>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="flex items-start justify-between py-3">
                    <div class="flex-1">
                        <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Last Updated</dt>
                        <dd class="text-base font-medium text-slate-900">{{ $user->updated_at->format('F j, Y') }}</dd>
                        <dd class="text-xs text-slate-500 mt-1">{{ $user->updated_at->format('g:i A') }}</dd>
                    </div>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
            </dl>
        </div>

        <!-- Quick Actions Card -->
        <div class="glass-card info-card">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 rounded-xl bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Quick Actions</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('admin.profile.edit') }}" class="group flex items-center justify-between p-4 rounded-xl border-2 border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-indigo-600 text-white group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Edit Profile</p>
                            <p class="text-xs text-slate-600">Update your information</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                
                <button onclick="alert('Password reset functionality coming soon')" class="group w-full flex items-center justify-between p-4 rounded-xl border-2 border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-slate-600 text-white group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Change Password</p>
                            <p class="text-xs text-slate-600">Update security settings</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-slate-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection
