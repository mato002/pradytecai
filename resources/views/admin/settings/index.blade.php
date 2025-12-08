@extends('layouts.admin')

@section('title', 'Settings - Admin')
@section('page_title', 'Settings')
@section('page_description', 'Manage site settings and configuration')

@push('styles')
<style>
    .settings-nav-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    .settings-nav-item {
        position: relative;
        transition: all 0.2s ease;
        border-radius: 0.5rem;
    }
    
    .settings-nav-item:hover {
        background-color: #f8fafc;
    }
    
    .settings-nav-item.active {
        background-color: #f1f5f9;
    }
    
    .settings-nav-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #6366f1;
        border-radius: 0 2px 2px 0;
    }
    
    .settings-content-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
    }
    
    .settings-content-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .settings-icon {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }
    
    .settings-icon-general {
        background-color: #eef2ff;
        color: #6366f1;
    }
    
    .settings-icon-hero {
        background-color: #fdf2f8;
        color: #ec4899;
    }
    
    .settings-link {
        color: #3b82f6;
        font-weight: 500;
        transition: color 0.2s ease;
    }
    
    .settings-link:hover {
        color: #2563eb;
    }
</style>
@endpush

@section('content')
    <div class="grid lg:grid-cols-4 gap-6">
        <!-- Settings Navigation -->
        <div class="settings-nav-card p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="settings-icon settings-icon-general">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900">Categories</h3>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('admin.settings.general') }}" 
                   class="settings-nav-item block px-4 py-3 {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="settings-icon settings-icon-general">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-900">General</div>
                            <div class="text-xs text-slate-500 mt-0.5">Site settings</div>
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin.settings.hero.edit') }}" 
                   class="settings-nav-item block px-4 py-3 {{ request()->routeIs('admin.settings.hero.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="settings-icon settings-icon-hero">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-900">Hero</div>
                            <div class="text-xs text-slate-500 mt-0.5">Homepage hero</div>
                        </div>
                    </div>
                </a>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Settings Dashboard Card -->
            <div class="settings-content-card p-6">
                <div class="flex items-center gap-4">
                    <div class="settings-icon settings-icon-general">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Settings Dashboard</h3>
                        <p class="text-sm text-slate-600 mt-1">Configure and manage your site settings</p>
                    </div>
                </div>
            </div>

            <!-- Settings Cards Grid -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- General Settings Card -->
                <a href="{{ route('admin.settings.general') }}" class="settings-content-card p-6 block">
                    <div class="flex items-start gap-4">
                        <div class="settings-icon settings-icon-general flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-semibold text-slate-900 mb-1">General Settings</h4>
                            <p class="text-sm text-slate-600 mb-3 leading-relaxed">
                                Configure site-wide settings, contact information and maintenance mode.
                            </p>
                            <span class="settings-link text-sm inline-flex items-center gap-1">
                                Configure
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Hero Settings Card -->
                <a href="{{ route('admin.settings.hero.edit') }}" class="settings-content-card p-6 block">
                    <div class="flex items-start gap-4">
                        <div class="settings-icon settings-icon-hero flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-semibold text-slate-900 mb-1">Hero Settings</h4>
                            <p class="text-sm text-slate-600 mb-3 leading-relaxed">
                                Customize the homepage hero section with images and content.
                            </p>
                            <span class="settings-link text-sm inline-flex items-center gap-1">
                                Configure
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
