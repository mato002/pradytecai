@extends('layouts.app')

@section('title', 'Apply for ' . $position->title . ' - Pradytec')
@section('description', 'Apply for the ' . $position->title . ' position at Pradytec.')

@section('content')
<x-marketing.page-hero
    :title="'Apply for ' . $position->title"
    :subtitle="$position->type . ' · ' . $position->location"
    :breadcrumbs="[
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Careers', 'url' => route('careers.index')],
        ['label' => 'Apply'],
    ]"
/>
<!-- Application Form -->
    <section class="mkt-section mkt-section--light">
        <div class="mkt-container max-w-4xl">
            @if(session('success'))
                <div class="mb-8 rounded-xl border-2 border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-medium text-emerald-800 flex items-center gap-3 shadow-lg">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Application Submitted Successfully!</p>
                        <p class="text-emerald-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-xl shadow-slate-200/50 p-6 lg:p-10">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2 flex items-center gap-3">
                        <svg class="w-8 h-8 text-[var(--prady-blue)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Application Form
                    </h2>
                    <p class="text-slate-600">Please fill out all required fields to submit your application</p>
                </div>

                <form action="{{ route('careers.apply.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <input type="hidden" name="position_id" value="{{ $position->id }}">

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[var(--prady-blue)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Personal Information
                            </h3>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-slate-700">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" 
                                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-400 transition-all duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-[var(--prady-cyan)]/20 focus:outline-none hover:border-slate-400">
                                @error('name')
                                    <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-semibold text-slate-700">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" 
                                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-400 transition-all duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-[var(--prady-cyan)]/20 focus:outline-none hover:border-slate-400">
                                @error('email')
                                    <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-semibold text-slate-700">
                                Phone Number
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                                   class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-400 transition-all duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-[var(--prady-cyan)]/20 focus:outline-none hover:border-slate-400"
                                   placeholder="+1 (555) 123-4567">
                            <p class="mt-1.5 text-xs text-slate-500">Optional but recommended for faster communication</p>
                            @error('phone')
                                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-slate-200 pt-8 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[var(--prady-blue)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Documents & Application
                            </h3>
                        </div>

                        <div class="space-y-2">
                            <label for="resume" class="block text-sm font-semibold text-slate-700">
                                Resume/CV <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" id="resume" name="resume" required accept=".pdf,.doc,.docx" 
                                       class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[var(--prady-ice)] file:text-[var(--prady-navy)] hover:file:bg-[var(--prady-ice)] transition-all duration-200 focus:border-indigo-500 focus:ring-4 focus:ring-[var(--prady-cyan)]/20 focus:outline-none hover:border-slate-400">
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Accepted formats: PDF, DOC, DOCX (Max 5MB)
                            </p>
                            @error('resume')
                                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="cover_letter" class="block text-sm font-semibold text-slate-700">
                                Cover Letter <span class="text-red-500">*</span>
                            </label>
                            <textarea id="cover_letter" name="cover_letter" required rows="10" 
                                      class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base text-slate-900 placeholder-slate-400 transition-all duration-200 resize-y focus:border-indigo-500 focus:ring-4 focus:ring-[var(--prady-cyan)]/20 focus:outline-none hover:border-slate-400"
                                      placeholder="Tell us why you're interested in this position and what makes you a great fit...">{{ old('cover_letter') }}</textarea>
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Minimum 50 characters. Be specific about why you're interested in this role.
                            </p>
                            @error('cover_letter')
                                <p class="mt-1.5 text-xs font-medium text-red-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-200">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-[var(--prady-navy)] to-[var(--prady-blue)] text-white text-base font-semibold shadow-lg shadow-[rgba(5,49,113,0.2)] transition-all duration-200 hover:from-[var(--prady-navy)] hover:to-[var(--prady-deep-blue)] hover:shadow-xl hover:shadow-[rgba(5,49,113,0.28)] hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-[var(--prady-cyan)]/20 active:scale-[0.98]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Application
                        </button>
                        <a href="{{ route('careers.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl border-2 border-slate-300 bg-white text-slate-700 text-base font-semibold transition-all duration-200 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
