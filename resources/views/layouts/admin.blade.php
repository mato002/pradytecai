<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <a href="/" class="block text-slate-900" data-turbo="false">
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
            <p class="mt-4 text-base text-slate-700">Marketing Command Centre for products, campaigns, content and leads.</p>

            @include('admin.partials.sidebar-nav')

            <div class="mt-10 border-t border-slate-200 pt-4 text-xs text-slate-500">
                <p class="font-medium text-slate-700">© {{ date('Y') }} Pradytecai</p>
                <p>Marketing Command Centre • v2.0</p>
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
            <turbo-frame id="admin_main" data-turbo-action="advance">
                <header class="admin-header flex-shrink-0">
                    <div class="admin-header__inner">
                        <div class="space-y-3">
                            <span class="badge-soft">@yield('page_eyebrow', 'Operations cockpit')</span>
                            <div class="flex flex-wrap items-center gap-4">
                                <h1 class="text-4xl font-bold text-slate-900">@yield('page_title', 'Dashboard')</h1>
                                @can('products.view')
                                    @include('admin.partials.product-switcher')
                                @endcan
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
                                    <form id="admin-search-form" class="flex items-center gap-2 rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 hover:border-indigo-400 focus-within:border-indigo-500 transition w-full" role="search" data-turbo-frame="admin_main">
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
                                            <a href="#" id="admin-search-view-all" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold" data-turbo-frame="admin_main">
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
                                        <a href="{{ route('admin.enquiries.index') }}" class="btn-ghost" data-turbo-frame="admin_main">
                                            Review enquiries
                                        </a>
                                        <a href="{{ route('admin.blog.create') }}" class="btn-primary" data-turbo-frame="admin_modal">
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
                                           class="block rounded-xl px-4 py-2 text-slate-700 hover:bg-slate-100"
                                           data-turbo-frame="admin_main">View profile</a>
                                        <a href="{{ route('admin.settings.index') }}"
                                           class="block rounded-xl px-4 py-2 text-slate-700 hover:bg-slate-100"
                                           data-turbo-frame="admin_main">Settings</a>
                                        <form method="POST" action="{{ route('logout') }}" data-turbo="false">
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
                        @include('admin.partials.flash-data')
                        @yield('content')
                    </div>
                </main>
            </turbo-frame>

            <footer class="admin-footer flex-shrink-0">
                <div class="admin-footer__inner flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <span>Pradytecai Admin Surface</span>
                    <span class="text-slate-500">Built on Laravel & Tailwind</span>
                </div>
            </footer>
        </div>
    </div>

    {{-- Modal overlay + Turbo frame for create/edit forms --}}
    <div id="admin-modal-overlay"
         class="admin-modal-overlay hidden"
         role="dialog"
         aria-modal="true"
         aria-hidden="true">
        <turbo-frame id="admin_modal">
            @if(is_admin_modal_request() && (session('success') || session('error')))
                <div hidden
                     data-admin-modal-complete
                     data-success="{{ session('success') }}"
                     data-error="{{ session('error') }}"
                     data-refresh-url="{{ url()->current() }}"></div>
            @endif
        </turbo-frame>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('admin-sidebar');
            const sidebarClose = document.getElementById('admin-sidebar-close');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const sidebarToggle = document.getElementById('admin-sidebar-toggle');
            const sidebarToggleFloating = document.getElementById('admin-sidebar-toggle-floating');
            let searchTimeout;

            function el(id) {
                return document.getElementById(id);
            }

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
                const searchResults = el('admin-search-results');
                const searchItems = el('admin-search-items');
                const searchLoading = el('admin-search-loading');
                const searchEmpty = el('admin-search-empty');
                const searchFooter = el('admin-search-footer');
                const searchViewAll = el('admin-search-view-all');

                if (!searchResults || !searchItems || !searchLoading) return;
                if (query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchLoading.classList.remove('hidden');
                searchItems.innerHTML = '';
                searchEmpty?.classList.add('hidden');
                searchFooter?.classList.add('hidden');
                searchResults.classList.remove('hidden');

                fetch(`{{ route('admin.search.api') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.classList.add('hidden');
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(item => {
                                const resultItem = document.createElement('a');
                                resultItem.href = item.url;
                                resultItem.setAttribute('data-turbo-frame', 'admin_main');
                                resultItem.className = 'flex items-start gap-3 px-4 py-3 hover:bg-indigo-50 transition border-b border-slate-100 last:border-0';
                                resultItem.innerHTML = `
                                    <div class="flex-shrink-0 text-indigo-600 mt-0.5">${getIcon(item.icon)}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-base font-semibold text-slate-900">${item.title}</div>
                                        <div class="text-sm text-slate-600 mt-0.5">${item.subtitle}</div>
                                    </div>`;
                                searchItems.appendChild(resultItem);
                            });
                            if (searchViewAll) {
                                searchViewAll.href = `{{ route('admin.search') }}?q=${encodeURIComponent(query)}`;
                                searchFooter?.classList.remove('hidden');
                            }
                        } else {
                            searchEmpty?.classList.remove('hidden');
                        }
                    })
                    .catch(() => {
                        searchLoading.classList.add('hidden');
                        searchEmpty?.classList.remove('hidden');
                    });
            }

            function closeProfileMenu() {
                el('profile-menu')?.classList.add('hidden');
            }

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                backdrop?.classList.add('opacity-100', 'pointer-events-auto');
            }

            function closeSidebar() {
                sidebar?.classList.add('-translate-x-full');
                backdrop?.classList.remove('opacity-100', 'pointer-events-auto');
            }

            function toggleSidebar() {
                document.body.classList.toggle('admin-sidebar-collapsed');
                const toggleIcon = el('sidebar-toggle-icon');
                const collapsed = document.body.classList.contains('admin-sidebar-collapsed');
                if (toggleIcon) {
                    toggleIcon.innerHTML = collapsed
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h10M4 12h10M4 18h10" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h10M10 12h10M10 18h10" />';
                }
                if (sidebarToggleFloating) {
                    sidebarToggleFloating.classList.toggle('hidden', !collapsed);
                    sidebarToggleFloating.classList.toggle('flex', collapsed);
                }
                try {
                    localStorage.setItem('adminSidebarCollapsed', collapsed ? '1' : '0');
                } catch (_) {}
            }

            function getTheme() {
                try { return localStorage.getItem('adminTheme') || 'light'; } catch (_) { return 'light'; }
            }

            function setTheme(theme) {
                const body = document.body;
                body.setAttribute('data-theme', theme);
                body.classList.remove('theme-dark', 'theme-light');
                body.classList.add('theme-' + theme);
                try { localStorage.setItem('adminTheme', theme); } catch (_) {}
                const themeIconDark = el('theme-icon-dark');
                const themeIconLight = el('theme-icon-light');
                if (theme === 'light') {
                    themeIconDark?.classList.add('hidden');
                    themeIconLight?.classList.remove('hidden');
                } else {
                    themeIconDark?.classList.remove('hidden');
                    themeIconLight?.classList.add('hidden');
                }
            }

            // Restore sidebar + theme
            try {
                if (localStorage.getItem('adminSidebarCollapsed') === '1') {
                    document.body.classList.add('admin-sidebar-collapsed');
                    const toggleIcon = el('sidebar-toggle-icon');
                    if (toggleIcon) {
                        toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h10M4 12h10M4 18h10" />';
                    }
                    sidebarToggleFloating?.classList.remove('hidden');
                    sidebarToggleFloating?.classList.add('flex');
                }
            } catch (_) {}
            setTheme(getTheme());

            // Event delegation — survives Turbo frame replacements of the header
            document.addEventListener('click', function (event) {
                const target = event.target;

                if (target.closest('#profile-menu-button')) {
                    event.stopPropagation();
                    el('profile-menu')?.classList.toggle('hidden');
                    return;
                }

                if (!target.closest('#profile-menu')) {
                    closeProfileMenu();
                }

                if (target.closest('#admin-mobile-menu-button')) {
                    event.stopPropagation();
                    openSidebar();
                    return;
                }

                if (target.closest('#admin-sidebar-toggle') || target.closest('#admin-sidebar-toggle-floating')) {
                    event.preventDefault();
                    event.stopPropagation();
                    toggleSidebar();
                    return;
                }

                if (target.closest('#theme-toggle')) {
                    event.preventDefault();
                    event.stopPropagation();
                    const current = document.body.getAttribute('data-theme') || 'dark';
                    setTheme(current === 'dark' ? 'light' : 'dark');
                    return;
                }

                const searchForm = el('admin-search-form');
                const searchResults = el('admin-search-results');
                if (searchForm && searchResults && !searchForm.contains(target) && !searchResults.contains(target)) {
                    searchResults.classList.add('hidden');
                }
            });

            document.addEventListener('input', function (event) {
                if (event.target?.id !== 'admin-search-input') return;
                clearTimeout(searchTimeout);
                const query = event.target.value.trim();
                searchTimeout = setTimeout(() => performSearch(query), 300);
            });

            document.addEventListener('focusin', function (event) {
                if (event.target?.id !== 'admin-search-input') return;
                const query = event.target.value.trim();
                if (query.length >= 2) performSearch(query);
            });

            document.addEventListener('submit', function (event) {
                if (event.target?.id !== 'admin-search-form') return;
                event.preventDefault();
                const query = el('admin-search-input')?.value.trim();
                if (!query) return;
                if (window.Turbo?.visit) {
                    Turbo.visit(`{{ route('admin.search') }}?q=${encodeURIComponent(query)}`, { frame: 'admin_main' });
                } else {
                    window.location.href = `{{ route('admin.search') }}?q=${encodeURIComponent(query)}`;
                }
            });

            sidebarClose?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (event) {
                if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
                    event.preventDefault();
                    el('admin-search-input')?.focus();
                }
                if (event.key === 'Escape') {
                    closeSidebar();
                    closeProfileMenu();
                    el('admin-search-results')?.classList.add('hidden');
                }
            });

            document.addEventListener('turbo:frame-load', function (event) {
                if (event.target.id === 'admin_main') {
                    setTheme(getTheme());
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
