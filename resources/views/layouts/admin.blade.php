<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Pradytecai')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="admin-shell font-sans" data-theme="dark">
    <div class="relative isolate min-h-screen lg:flex">
        <div id="admin-sidebar-backdrop"
             class="fixed inset-0 z-30 bg-slate-950/80 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

        <!-- Sidebar -->
         
        <aside id="admin-sidebar"
               class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-white/10 bg-slate-950/80 p-6 shadow-2xl shadow-slate-950/70 backdrop-blur-2xl transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0 lg:shadow-none">
            <div class="flex items-start justify-between">
                <a href="/" class="block text-white">
                    <span class="text-xs uppercase tracking-[0.3em] text-sky-400">Pradytecai</span>
                    <span class="mt-2 block text-2xl font-semibold leading-tight">Admin Control</span>
                </a>
                <button id="admin-sidebar-close"
                        class="btn-ghost lg:hidden px-2 py-2 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="mt-4 text-sm text-slate-400">Command centre for products, content, enquiries, and hiring.</p>

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
                                    <span class="block text-base text-white">Dashboard</span>
                                    <span class="text-xs font-normal text-slate-400">Mission control</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="block text-base text-white">Products</span>
                                    <span class="text-xs font-normal text-slate-400">Solutions catalog</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="block text-base text-white">Enquiries</span>
                                    <span class="text-xs font-normal text-slate-400">Client pipeline</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="block text-base text-white">Users</span>
                                    <span class="text-xs font-normal text-slate-400">Admin accounts</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="block text-base text-white">Blog</span>
                                    <span class="text-xs font-normal text-slate-400">Content lab</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="block text-base text-white">Positions</span>
                                    <span class="text-xs font-normal text-slate-400">Hiring board</span>
                                </span>
                            </span>
                            <svg class="w-4 h-4 text-white/70 sidebar-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <div class="mt-10 border-t border-white/10 pt-4 text-xs text-slate-500">
                <p class="font-medium text-slate-300">© {{ date('Y') }} Pradytecai</p>
                <p>Admin cockpit • v1.1</p>
            </div>
        </aside>

        <!-- Main area -->
        <div class="relative z-10 flex min-h-screen flex-1 flex-col lg:ml-0">
            <header class="admin-header">
                <div class="admin-header__inner">
                    <div class="space-y-3">
                        <span class="badge-soft">@yield('page_eyebrow', 'Operations cockpit')</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-3xl font-semibold text-white">@yield('page_title', 'Dashboard')</h1>
                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-200">
                                <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                Live sync
                            </span>
                        </div>
                        <p class="max-w-2xl text-sm text-slate-300">@yield('page_description', 'Monitor every product stream, enquiry and talent conversation from one elevated surface.')</p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-end gap-3">
                            <button id="admin-mobile-menu-button" class="btn-ghost lg:hidden" type="button">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Menu
                            </button>
                            <form class="hidden md:flex items-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-4 py-2 backdrop-blur" role="search">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                                <input type="text" placeholder="Search workspace" class="w-48 bg-transparent text-sm text-slate-100 placeholder-slate-500 focus:outline-none">
                                <span class="text-[10px] rounded-full border border-white/15 px-2 py-0.5 text-slate-400">⌘ + K</span>
                            </form>
                        </div>
                        <div class="flex flex-wrap items-center justify-end gap-3">
                            <button id="admin-sidebar-toggle" class="btn-ghost hidden lg:inline-flex" type="button">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h10M4 18h7" />
                                </svg>
                            </button>
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
                                        class="flex items-center gap-3 rounded-full border border-white/15 bg-white/5 px-3 py-2 text-left text-sm font-medium text-slate-200">
                                    <span class="hidden sm:flex flex-col">
                                        <span>{{ auth()->user()->name ?? 'Admin User' }}</span>
                                        <span class="text-xs text-slate-400">{{ auth()->user()->email ?? 'admin@example.com' }}</span>
                                    </span>
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-500 text-xs font-semibold uppercase text-white">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                                    </span>
                                </button>

                                <div id="profile-menu"
                                     class="hidden absolute right-0 mt-3 w-56 rounded-2xl border border-white/10 bg-slate-900/95 p-2 text-sm shadow-2xl backdrop-blur">
                                    <a href="#"
                                       class="block rounded-xl px-4 py-2 text-slate-200 hover:bg-white/5">View profile</a>
                                    <a href="#"
                                       class="block rounded-xl px-4 py-2 text-slate-200 hover:bg-white/5">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="mt-1 w-full rounded-xl px-4 py-2 text-left text-red-300 hover:bg-red-500/10">
                                            Logout
                                        </button>
                                    </form>
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

            <footer class="admin-footer">
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
            const sidebar = document.getElementById('admin-sidebar');
            const sidebarClose = document.getElementById('admin-sidebar-close');
            const backdrop = document.getElementById('admin-sidebar-backdrop');

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

            function toggleCollapsed() {
                document.body.classList.toggle('admin-shell--collapsed');
                try {
                    localStorage.setItem(
                        'adminSidebarCollapsed',
                        document.body.classList.contains('admin-shell--collapsed') ? '1' : '0'
                    );
                } catch (_) {}
            }

            // Restore collapsed state
            try {
                if (localStorage.getItem('adminSidebarCollapsed') === '1') {
                    document.body.classList.add('admin-shell--collapsed');
                }
            } catch (_) {}

            mobileToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                openSidebar();
            });

            sidebarToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                toggleCollapsed();
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
                    return localStorage.getItem('adminTheme') || 'dark';
                } catch (_) {
                    return 'dark';
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

    @stack('scripts')
</body>
</html>


