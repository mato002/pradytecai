@extends('layouts.admin')

@section('title', 'General Settings - Admin')
@section('page_title', 'General Settings')
@section('page_description', 'Configure site-wide settings and preferences')

@push('styles')
<style>
    .form-section {
        position: relative;
        padding: 2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    
    .form-input-enhanced {
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-input-enhanced:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        border-color: #6366f1;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.4s;
        border-radius: 34px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    input:checked + .toggle-slider {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
    
    .section-header {
        position: relative;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .section-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
        border-radius: 2px;
    }
</style>
@endpush

@section('content')
    <div class="glass-card">
        @if(session('success'))
            <div class="mb-6 rounded-2xl border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 to-green-50 px-6 py-4 shadow-lg animate-slide-down">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-emerald-900">Settings Saved!</p>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Site Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Site Information</h3>
                    </div>
                    <p class="text-sm text-slate-600">Basic information about your website</p>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Site Name
                            <span class="text-slate-400 font-normal">(required)</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="site_name" 
                                name="site_name" 
                                value="{{ old('site_name', 'Pradytecai') }}" 
                                required
                                class="form-input-enhanced w-full px-4 py-3 pl-12 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-200 outline-none"
                            >
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            This appears in browser tabs and search results
                        </p>
                    </div>

                    <div>
                        <label for="site_description" class="block text-sm font-semibold text-slate-700 mb-2">
                            Site Description
                        </label>
                        <textarea 
                            id="site_description" 
                            name="site_description" 
                            rows="3"
                            class="form-input-enhanced w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-200 outline-none resize-none"
                        >{{ old('site_description', 'Enterprise software solutions for modern businesses.') }}</textarea>
                        <p class="mt-2 text-xs text-slate-500">Brief description for SEO and social sharing</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Contact Information</h3>
                    </div>
                    <p class="text-sm text-slate-600">How visitors can reach you</p>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_email" class="block text-sm font-semibold text-slate-700 mb-2">
                            Contact Email
                        </label>
                        <div class="relative">
                            <input 
                                type="email" 
                                id="contact_email" 
                                name="contact_email" 
                                value="{{ old('contact_email', 'mathiasodhis@gmail.com') }}" 
                                required
                                class="form-input-enhanced w-full px-4 py-3 pl-12 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-200 outline-none"
                            >
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-semibold text-slate-700 mb-2">
                            Contact Phone
                        </label>
                        <div class="relative">
                            <input 
                                type="tel" 
                                id="contact_phone" 
                                name="contact_phone" 
                                value="{{ old('contact_phone', '+254 728 883 160') }}" 
                                class="form-input-enhanced w-full px-4 py-3 pl-12 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-200 outline-none"
                            >
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Mode Section -->
            <div class="form-section">
                <div class="section-header">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Maintenance Mode</h3>
                    </div>
                    <p class="text-sm text-slate-600">Control site accessibility</p>
                </div>
                
                <div class="flex items-center justify-between p-6 rounded-xl bg-gradient-to-r from-slate-50 to-slate-100 border-2 border-slate-200">
                    <div class="flex-1">
                        <h4 class="font-semibold text-slate-900 mb-1">Enable Maintenance Mode</h4>
                        <p class="text-sm text-slate-600">When enabled, the site will be unavailable to visitors. Only admins can access.</p>
                    </div>
                    <label class="toggle-switch ml-4">
                        <input type="checkbox" name="maintenance_mode" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t-2 border-slate-200">
                <a href="{{ route('admin.settings.index') }}" class="btn-ghost w-full sm:w-auto flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Settings
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection
