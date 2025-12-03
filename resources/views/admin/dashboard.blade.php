@extends('layouts.admin')

@section('title', 'Admin Dashboard - Pradytecai')
@section('page_title', 'Dashboard')

@section('content')
    <div class="grid md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-600 mb-2">Total Users</h2>
            <p class="text-4xl font-bold text-slate-900">123</p>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Admin accounts with access.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-600 mb-2">Active Products</h2>
            <p class="text-4xl font-bold text-slate-900">5</p>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Visible on the marketing site.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-600 mb-2">New Enquiries (7d)</h2>
            <p class="text-4xl font-bold text-slate-900">8</p>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Contact form submissions.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-600 mb-2">Published Blog Posts</h2>
            <p class="text-4xl font-bold text-slate-900">6</p>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Articles live on the blog.</p>
        </div>
    </div>

    <div class="mt-8 grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Recent Enquiries</h2>
            <div class="space-y-3 text-sm md:text-base">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-slate-900">Jane Doe</p>
                        <p class="text-xs text-slate-500">BulkSMS CRM demo • 2 hours ago</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs">New</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-slate-900">Acme Microfinance</p>
                        <p class="text-xs text-slate-500">Prady Mfi onboarding • Yesterday</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">In progress</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h2>
            <div class="space-y-3 text-sm md:text-base">
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4" />
                        </svg>
                        Add new product
                    </span>
                    <span class="text-xs text-slate-400">Products</span>
                </a>
                <a href="{{ route('admin.enquiries.index') }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h4.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0118 10.414V18a2 2 0 01-2 2z" />
                        </svg>
                        Review enquiries
                    </span>
                    <span class="text-xs text-slate-400">Enquiries</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
                        </svg>
                        Manage admin users
                    </span>
                    <span class="text-xs text-slate-400">Users</span>
                </a>
            </div>
        </div>
    </div>
@endsection


