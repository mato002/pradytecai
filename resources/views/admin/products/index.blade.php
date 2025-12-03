@extends('layouts.admin')

@section('title', 'Admin - Products')
@section('page_title', 'Products')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Products</h2>
            <p class="text-sm text-slate-500">Manage the solutions and products shown on the marketing site.</p>
        </div>
        <a href="#"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            <span class="mr-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </span>
            Add Product
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm md:text-base">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="px-4 py-3">BulkSMS CRM</td>
                    <td class="px-4 py-3">Messaging Platform</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">Active</span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Edit</button>
                        <button class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50">Disable</button>
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3">Prady Mfi</td>
                    <td class="px-4 py-3">Microfinance System</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">Active</span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Edit</button>
                        <button class="inline-flex items-center px-3 py-1.5 rounded-lg border border-red-100 text-red-600 hover:bg-red-50">Disable</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
