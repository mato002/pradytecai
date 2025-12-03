<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Pradytecai')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-col w-64 bg-slate-900 text-slate-100">
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <a href="/" class="text-xl font-bold text-white">
                    Pradytecai<span class="text-indigo-400"> Admin</span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                    <span class="mr-2">
                        <!-- Home icon -->
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4-4h8" />
                        </svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.products.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                    <span class="mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </span>
                    Products
                </a>
                <a href="{{ route('admin.enquiries.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.enquiries.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                    <span class="mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-2a4 4 0 014-4h6m-6 0l2-2m-2 2l2 2" />
                        </svg>
                    </span>
                    Enquiries
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                    <span class="mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a3 3 0 10-6 0 3 3 0 006 0z" />
                        </svg>
                    </span>
                    Users
                </a>
                <a href="{{ route('admin.blog.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.blog.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">
                    <span class="mr-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 20l9-5-9-5-9 5 9 5zm0-10l9-5-9-5-9 5 9 5z" />
                        </svg>
                    </span>
                    Blog
                </a>
            </nav>
            <div class="px-4 py-4 border-t border-slate-800 text-xs text-slate-400">
                &copy; {{ date('Y') }} Pradytecai. Admin Panel.
            </div>
        </aside>

        <!-- Main area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 bg-white border-b border-slate-200">
                <div class="flex items-center space-x-3">
                    <!-- Mobile sidebar toggle (placeholder, JS optional) -->
                    <button id="admin-mobile-menu-button" class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-900">
                        @yield('page_title', 'Dashboard')
                    </h1>
                </div>

                <!-- Profile dropdown -->
                <div class="relative">
                    <button id="profile-menu-button"
                            class="flex items-center space-x-3 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm md:text-base hover:bg-slate-50">
                        <span class="hidden sm:flex flex-col text-left">
                            <span class="font-medium text-slate-900">
                                {{ auth()->user()->name ?? 'Admin User' }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ auth()->user()->email ?? 'admin@example.com' }}
                            </span>
                        </span>
                        <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </span>
                    </button>

                    <div id="profile-menu"
                         class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm z-20">
                        <a href="#"
                           class="block px-4 py-2 text-slate-700 hover:bg-slate-50">
                            View profile
                        </a>
                        <a href="#"
                           class="block px-4 py-2 text-slate-700 hover:bg-slate-50">
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 text-sm md:text-base">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 bg-white px-4 sm:px-6 lg:px-8 py-3 text-xs text-slate-500 flex items-center justify-between">
                <span>Pradytecai Admin</span>
                <span>Built with Laravel</span>
            </footer>
        </div>
    </div>

    <script>
        // Simple profile dropdown toggle
        (function () {
            const btn = document.getElementById('profile-menu-button');
            const menu = document.getElementById('profile-menu');
            if (!btn || !menu) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function () {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>


