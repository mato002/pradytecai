<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Pradytecai')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-shell font-sans" data-theme="light">
    <div class="relative isolate min-h-screen lg:flex flex-1 w-full">
        <div id="admin-sidebar-backdrop"
             class="fixed inset-0 z-30 bg-slate-900/80 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

        <!-- Sidebar -->
         
        <aside id="admin-sidebar"
               class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/50 transition-all duration-300 ease-in-out -translate-x-full lg:fixed lg:translate-x-0 lg:shadow-none">
            <div class="flex items-start justify-between">
                <a href="/" class="block text-slate-900">
                    <span class="text-sm uppercase tracking-wide font-semibold text-indigo-700">Pradytecai</span>
                    <span class="mt-2 block text-2xl font-bold leading-tight text-slate-900">Admin Control</span>
                </a>
                <div class="flex items-center gap-2 ml-auto">
                    <button id="admin-sidebar-toggle" class="btn-ghost hidden lg:inline-flex px-3 py-2 rounded-xl" type="button" title="Hide sidebar" style="pointer-events: auto !important; z-index: 100 !important; position: relative; cursor: pointer;">
                        <svg id="sidebar-toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <button id="admin-sidebar-close"
                            class="btn-ghost lg:hidden px-2 py-2 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <p class="mt-4 text-base text-slate-700">Command centre for products, content, enquiries, and hiring.</p>

            <nav class="mt-8 flex-1 space-y-8 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-3">Workspace</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-pill {{ request()->routeIs('admin.dashboard') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10h4v-6h6v6h4V10" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Dashboard</span>
                                    <span class="text-sm font-normal text-slate-700">Mission control</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.products.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.products.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Products</span>
                                    <span class="text-sm font-normal text-slate-700">Solutions catalog</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.enquiries.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.enquiries.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 10a8.38 8.38 0 01-.9 3.8l-3.2 6.4a1 1 0 01-1.8 0l-3.2-6.4A8.38 8.38 0 0111 10a8 8 0 1110 0z" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Enquiries</span>
                                    <span class="text-sm font-normal text-slate-700">Client pipeline</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.users.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M12 11a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Users</span>
                                    <span class="text-sm font-normal text-slate-700">Admin accounts</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.blog.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.blog.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 20l9-5-9-5-9 5 9 5zm0-10l9-5-9-5-9 5 9 5z" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Blog</span>
                                    <span class="text-sm font-normal text-slate-700">Content lab</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.positions.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.positions.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2M5 21h14a2 2 0 002-2v-8H3v8a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Positions</span>
                                    <span class="text-sm font-normal text-slate-700">Hiring board</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.applications.index') }}"
                           class="nav-pill {{ request()->routeIs('admin.applications.*') ? 'nav-pill--active' : '' }}">
                            <span class="flex items-center gap-3">
                                <span class="nav-pill__icon">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <span class="sidebar-text">
                                    <span class="block text-lg font-semibold text-slate-900">Applications</span>
                                    <span class="text-sm font-normal text-slate-700">Candidate reviews</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-3">Shortcuts</p>
                    <div class="space-y-2">
                        <a href="{{ route('admin.blog.create') }}" class="pill">
                            Start new blog post
                            <span class="text-xs text-slate-400">⌘ + B</span>
                        </a>
                        <a href="{{ route('admin.positions.create') }}" class="pill">
                            Add open role
                            <span class="text-xs text-slate-400">⌘ + R</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="pill">
                            Sync products
                            <span class="text-xs text-slate-400">⌘ + P</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="mt-10 border-t border-slate-200 pt-4 text-xs text-slate-500">
                <p class="font-medium text-slate-700">© {{ date('Y') }} Pradytecai</p>
                <p>Admin cockpit • v1.1</p>
            </div>
        </aside>

        <!-- Floating toggle button (shows when sidebar is hidden) -->
        <button id="admin-sidebar-toggle-floating" class="fixed top-4 left-4 z-50 hidden lg:flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 transition-all duration-300" type="button" title="Show sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Main area -->
        <div class="relative z-10 flex flex-1 flex-col w-full min-w-0 overflow-y-auto bg-white">
            <header class="admin-header flex-shrink-0">
                <div class="admin-header__inner">
                    <div class="space-y-3">
                        <span class="badge-soft">@yield('page_eyebrow', 'Operations cockpit')</span>
                        <div class="flex flex-wrap items-center gap-4">
                            <h1 class="text-4xl font-bold text-slate-900">@yield('page_title', 'Dashboard')</h1>
                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-800">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                Live sync
                            </span>
                        </div>
                        <p class="max-w-2xl text-base text-slate-700">@yield('page_description', 'Monitor every product stream, enquiry and talent conversation from one elevated surface.')</p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <!-- Centered Search Bar -->
                        <div class="flex items-center justify-center w-full">
                            <div class="relative w-full max-w-2xl">
                                <form id="admin-search-form" class="flex items-center gap-2 rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 hover:border-indigo-400 focus-within:border-indigo-500 transition w-full" role="search">
                                    <svg class="w-5 h-5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                    <input 
                                        type="text" 
                                        id="admin-search-input"
                                        name="q"
                                        placeholder="Search pages, content, users..." 
                                        value="{{ request('q') }}"
                                        autocomplete="off"
                                        class="bg-transparent text-base text-slate-900 placeholder-slate-500 focus:outline-none flex-1 min-w-0"
                                    >
                                    <span class="text-xs text-slate-400 hidden lg:inline">⌘K</span>
                                </form>
                                
                                <!-- Search Results Dropdown -->
                                <div id="admin-search-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border-2 border-slate-300 rounded-xl shadow-xl z-50 max-h-96 overflow-y-auto">
                                    <div id="admin-search-loading" class="hidden p-4 text-center text-slate-500">
                                        <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <div id="admin-search-items" class="py-2">
                                        <!-- Results will be inserted here -->
                                    </div>
                                    <div id="admin-search-empty" class="hidden p-4 text-center text-slate-500 text-sm">
                                        <p>No results found</p>
                                        <p class="text-xs text-slate-400 mt-1">Try different keywords</p>
                                    </div>
                                    <div id="admin-search-footer" class="hidden border-t border-slate-200 p-3 text-center">
                                        <a href="#" id="admin-search-view-all" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">
                                            View all results →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <button id="admin-mobile-menu-button" class="btn-ghost lg:hidden" type="button">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Menu
                            </button>
                            <div class="flex flex-wrap items-center justify-end gap-3">
                                <button id="theme-toggle" class="btn-ghost" type="button" title="Toggle theme">
                                <svg id="theme-icon-dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <svg id="theme-icon-light" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </button>
                                @hasSection('header_actions')
                                    @yield('header_actions')
                                @else
                                    <a href="{{ route('admin.enquiries.index') }}" class="btn-ghost">
                                        Review enquiries
                                    </a>
                                    <a href="{{ route('admin.blog.create') }}" class="btn-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4" />
                                        </svg>
                                        Create update
                                    </a>
                                @endif
                                <div class="relative">
                                <button id="profile-menu-button"
                                        class="flex items-center gap-3 rounded-full border border-slate-300 bg-white px-3 py-2 text-left text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    <span class="hidden sm:flex flex-col">
                                        <span>{{ auth()->user()->name ?? 'Admin User' }}</span>
                                        <span class="text-xs text-slate-500">{{ auth()->user()->email ?? 'admin@example.com' }}</span>
                                    </span>
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-500 text-xs font-semibold uppercase text-white">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                                    </span>
                                </button>

                                <div id="profile-menu"
                                     class="hidden absolute right-0 mt-3 w-56 rounded-2xl border border-slate-200 bg-white p-2 text-sm shadow-xl">
                                    <a href="{{ route('admin.profile.show') }}"
                                       class="block rounded-xl px-4 py-2 text-slate-700 hover:bg-slate-100">View profile</a>
                                    <a href="{{ route('admin.settings.index') }}"
                                       class="block rounded-xl px-4 py-2 text-slate-700 hover:bg-slate-100">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="mt-1 w-full rounded-xl px-4 py-2 text-left text-red-600 hover:bg-red-50">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="admin-main">
                <div class="admin-main__inner space-y-8">
                    @yield('content')
                </div>
            </main>

            <footer class="admin-footer flex-shrink-0">
                <div class="admin-footer__inner flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <span>Pradytecai Admin Surface</span>
                    <span class="text-slate-500">Built on Laravel & Tailwind</span>
                </div>
            </footer>
        </div>
    </div>

    <script>
        (function () {
            const profileBtn = document.getElementById('profile-menu-button');
            const profileMenu = document.getElementById('profile-menu');
            const mobileToggle = document.getElementById('admin-mobile-menu-button');
            const sidebarToggle = document.getElementById('admin-sidebar-toggle');
            const sidebarToggleFloating = document.getElementById('admin-sidebar-toggle-floating');
            const sidebar = document.getElementById('admin-sidebar');
            const sidebarClose = document.getElementById('admin-sidebar-close');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            
            // Live Search Functionality
            const searchInput = document.getElementById('admin-search-input');
            const searchForm = document.getElementById('admin-search-form');
            const searchResults = document.getElementById('admin-search-results');
            const searchItems = document.getElementById('admin-search-items');
            const searchLoading = document.getElementById('admin-search-loading');
            const searchEmpty = document.getElementById('admin-search-empty');
            const searchFooter = document.getElementById('admin-search-footer');
            const searchViewAll = document.getElementById('admin-search-view-all');
            let searchTimeout;
            
            function getIcon(type) {
                const icons = {
                    'page': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                    'blog': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5zm0-10l9-5-9-5-9 5 9 5z" /></svg>',
                    'user': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
                    'enquiry': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
                    'application': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                    'position': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
                };
                return icons[type] || icons['page'];
            }
            
            function performSearch(query) {
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                searchLoading.classList.remove('hidden');
                searchItems.innerHTML = '';
                searchEmpty.classList.add('hidden');
                searchFooter.classList.add('hidden');
                searchResults.classList.remove('hidden');
                
                fetch(`{{ route('admin.search.api') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.classList.add('hidden');
                        
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(item => {
                                const resultItem = document.createElement('a');
                                resultItem.href = item.url;
                                resultItem.className = 'flex items-start gap-3 px-4 py-3 hover:bg-indigo-50 transition border-b border-slate-100 last:border-0';
                                resultItem.innerHTML = `
                                    <div class="flex-shrink-0 text-indigo-600 mt-0.5">
                                        ${getIcon(item.icon)}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-base font-semibold text-slate-900">${item.title}</div>
                                        <div class="text-sm text-slate-600 mt-0.5">${item.subtitle}</div>
                                    </div>
                                `;
                                searchItems.appendChild(resultItem);
                            });
                            
                            if (searchViewAll) {
                                searchViewAll.href = `{{ route('admin.search') }}?q=${encodeURIComponent(query)}`;
                                searchFooter.classList.remove('hidden');
                            }
                        } else {
                            searchEmpty.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchLoading.classList.add('hidden');
                        searchEmpty.classList.remove('hidden');
                    });
            }
            
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();
                    
                    searchTimeout = setTimeout(() => {
                        performSearch(query);
                    }, 300); // Debounce 300ms
                });
                
                searchInput.addEventListener('focus', function() {
                    if (this.value.trim().length >= 2) {
                        performSearch(this.value.trim());
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchForm.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
                
                // Keyboard shortcut (Cmd/Ctrl + K)
                document.addEventListener('keydown', function(e) {
                    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                        e.preventDefault();
                        searchInput.focus();
                    }
                    
                    // Escape to close
                    if (e.key === 'Escape' && !searchResults.classList.contains('hidden')) {
                        searchResults.classList.add('hidden');
                        searchInput.blur();
                    }
                });
            }
            
            // Form submission - go to full search page
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query) {
                        window.location.href = `{{ route('admin.search') }}?q=${encodeURIComponent(query)}`;
                    }
                });
            }

            // Debug: Check if button exists
            console.log('Sidebar toggle button:', sidebarToggle);
            if (sidebarToggle) {
                console.log('Button found, adding click listener');
            }

            function closeProfileMenu() {
                profileMenu?.classList.add('hidden');
            }

            profileBtn?.addEventListener('click', function (event) {
                event.stopPropagation();
                profileMenu?.classList.toggle('hidden');
            });

            document.addEventListener('click', function () {
                closeProfileMenu();
            });

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                backdrop?.classList.add('opacity-100', 'pointer-events-auto');
            }

            function closeSidebar() {
                sidebar?.classList.add('-translate-x-full');
                backdrop?.classList.remove('opacity-100', 'pointer-events-auto');
            }

            function toggleSidebar() {
                const isHidden = document.body.classList.contains('admin-sidebar-hidden');
                document.body.classList.toggle('admin-sidebar-hidden');
                
                // Update toggle icon - hamburger when visible, X when hidden
                const toggleIcon = document.getElementById('sidebar-toggle-icon');
                if (toggleIcon) {
                    if (document.body.classList.contains('admin-sidebar-hidden')) {
                        // Sidebar is hidden, show hamburger icon (to open)
                        toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    } else {
                        // Sidebar is visible, show hamburger icon (to collapse)
                        toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    }
                }
                
                // Show/hide floating button
                if (sidebarToggleFloating) {
                    if (document.body.classList.contains('admin-sidebar-hidden')) {
                        sidebarToggleFloating.classList.remove('hidden');
                        sidebarToggleFloating.classList.add('flex');
                    } else {
                        sidebarToggleFloating.classList.add('hidden');
                        sidebarToggleFloating.classList.remove('flex');
                    }
                }
                
                try {
                    localStorage.setItem(
                        'adminSidebarHidden',
                        document.body.classList.contains('admin-sidebar-hidden') ? '1' : '0'
                    );
                } catch (_) {}
            }

            // Restore hidden state
            try {
                const toggleIcon = document.getElementById('sidebar-toggle-icon');
                if (localStorage.getItem('adminSidebarHidden') === '1') {
                    document.body.classList.add('admin-sidebar-hidden');
                    if (toggleIcon) {
                        toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    }
                    if (sidebarToggleFloating) {
                        sidebarToggleFloating.classList.remove('hidden');
                        sidebarToggleFloating.classList.add('flex');
                    }
                } else {
                    if (toggleIcon) {
                        toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                    }
                    if (sidebarToggleFloating) {
                        sidebarToggleFloating.classList.add('hidden');
                        sidebarToggleFloating.classList.remove('flex');
                    }
                }
            } catch (_) {}

            mobileToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                openSidebar();
            });

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('Sidebar toggle clicked');
                    toggleSidebar();
                });
                
                // Also try mousedown as backup
                sidebarToggle.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('Sidebar toggle mousedown');
                    toggleSidebar();
                });
            }

            sidebarToggleFloating?.addEventListener('click', function (event) {
                event.stopPropagation();
                toggleSidebar();
            });

            sidebarClose?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                    closeProfileMenu();
                }
            });

            // Theme toggle functionality
            const themeToggle = document.getElementById('theme-toggle');
            const themeIconDark = document.getElementById('theme-icon-dark');
            const themeIconLight = document.getElementById('theme-icon-light');
            const body = document.body;

            function getTheme() {
                try {
                    return localStorage.getItem('adminTheme') || 'light';
                } catch (_) {
                    return 'light';
                }
            }

            function setTheme(theme) {
                if (!body) return;
                
                // Set the data-theme attribute
                body.setAttribute('data-theme', theme);
                
                // Also add/remove a class for additional specificity
                body.classList.remove('theme-dark', 'theme-light');
                body.classList.add('theme-' + theme);
                
                try {
                    localStorage.setItem('adminTheme', theme);
                } catch (_) {}
                
                // Update icons
                if (theme === 'light') {
                    if (themeIconDark) themeIconDark.classList.add('hidden');
                    if (themeIconLight) themeIconLight.classList.remove('hidden');
                } else {
                    if (themeIconDark) themeIconDark.classList.remove('hidden');
                    if (themeIconLight) themeIconLight.classList.add('hidden');
                }
                
                console.log('Theme set to:', theme, 'Attribute:', body.getAttribute('data-theme'));
            }

            // Initialize theme
            const currentTheme = getTheme();
            setTheme(currentTheme);

            // Toggle theme on button click
            if (themeToggle) {
                themeToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const currentTheme = body.getAttribute('data-theme') || 'dark';
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    console.log('Toggling theme from', currentTheme, 'to', newTheme);
                    setTheme(newTheme);
                });
            }
        })();
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Handle delete and disable confirmations with SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            // Find all delete forms (by class or action attribute)
            const deleteForms = document.querySelectorAll('.delete-form, form[action*="destroy"], form[action*="delete"]');
            
            deleteForms.forEach(function(form) {
                // Skip if already has SweetAlert handler
                if (form.dataset.sweetalert === 'true') return;
                form.dataset.sweetalert = 'true';
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formAction = form.getAttribute('action');
                    const formMethod = form.querySelector('input[name="_method"]')?.value || 'POST';
                    
                    // Try to get item name from various sources
                    let itemName = 'this item';
                    const row = form.closest('tr');
                    if (row) {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            const titleElement = firstCell.querySelector('p.font-medium, .font-semibold, h3, h4') || firstCell;
                            itemName = titleElement.textContent.trim().split('\n')[0] || firstCell.textContent.trim().split('\n')[0];
                        }
                    }
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        html: `<div class="text-left"><p class="mb-2">You are about to delete:</p><p class="font-semibold text-slate-900">"${itemName}"</p><p class="mt-2 text-sm text-red-600">This action cannot be undone!</p></div>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="mr-2">🗑️</i> Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        backdrop: true,
                        background: '#ffffff',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl',
                            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold',
                            cancelButton: 'px-6 py-2.5 rounded-xl font-semibold',
                            backdrop: 'swal2-backdrop-show'
                        },
                        didOpen: () => {
                            // Make backdrop less opaque so page is visible
                            const backdrop = document.querySelector('.swal2-backdrop-show');
                            if (backdrop) {
                                backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                backdrop: true,
                                didOpen: () => {
                                    Swal.showLoading();
                                    const backdrop = document.querySelector('.swal2-backdrop-show');
                                    if (backdrop) {
                                        backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                                    }
                                }
                            });
                            
                            // Submit the form
                            form.submit();
                        }
                    });
                });
            });
            
            // Handle disable/enable toggle confirmations
            const toggleForms = document.querySelectorAll('.toggle-form, form[action*="toggle-status"], form[action*="toggle"]');
            
            toggleForms.forEach(function(form) {
                // Skip if already has SweetAlert handler
                if (form.dataset.sweetalert === 'true') return;
                form.dataset.sweetalert = 'true';
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const button = form.querySelector('button[type="submit"]');
                    const action = button?.textContent?.trim() || 'toggle';
                    const isDisable = action.toLowerCase().includes('disable');
                    
                    // Try to get item name
                    let itemName = 'this item';
                    const row = form.closest('tr');
                    if (row) {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            const titleElement = firstCell.querySelector('div.text-sm, p.font-semibold, .font-medium') || firstCell;
                            itemName = titleElement.textContent.trim().split('\n')[0];
                        }
                    }
                    
                    Swal.fire({
                        title: isDisable ? 'Disable this item?' : 'Enable this item?',
                        html: `<div class="text-left"><p class="mb-2">You are about to ${isDisable ? 'disable' : 'enable'}:</p><p class="font-semibold text-slate-900">"${itemName}"</p><p class="mt-2 text-sm text-slate-600">${isDisable ? 'It will be hidden from the public site. You can enable it again later.' : 'It will be visible on the public site.'}</p></div>`,
                        icon: isDisable ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: isDisable ? '#f59e0b' : '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: `Yes, ${action}!`,
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        backdrop: true,
                        background: '#ffffff',
                        customClass: {
                            popup: 'rounded-2xl shadow-2xl',
                            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold',
                            cancelButton: 'px-6 py-2.5 rounded-xl font-semibold',
                            backdrop: 'swal2-backdrop-show'
                        },
                        didOpen: () => {
                            // Make backdrop less opaque so page is visible
                            const backdrop = document.querySelector('.swal2-backdrop-show');
                            if (backdrop) {
                                backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>


