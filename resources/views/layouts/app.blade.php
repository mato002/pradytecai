<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pradytecai - Enterprise Software Solutions')</title>
    <meta name="description" content="@yield('description', 'Pradytecai offers cutting-edge software solutions including BulkSMS CRM, Microfinance Management, and more.')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        /* Simple hero fade/slide animation */
        @keyframes heroFadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-animate {
            opacity: 0;
            transform: translateY(24px);
            animation: heroFadeUp 0.8s ease-out forwards;
        }

        .hero-animate.delay-sm {
            animation-delay: 0.1s;
        }

        .hero-animate.delay-md {
            animation-delay: 0.2s;
        }

        .hero-animate.delay-lg {
            animation-delay: 0.3s;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-gray-900">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-b border-gray-200 z-50 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-blue-600">Pradytecai</a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('/') ? 'text-blue-600 font-semibold' : '' }}">Home</a>
                <a href="/about" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('about') ? 'text-blue-600 font-semibold' : '' }}">About</a>
                <a href="/services" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('services') ? 'text-blue-600 font-semibold' : '' }}">Services</a>
                <a href="/products" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('products') ? 'text-blue-600 font-semibold' : '' }}">Products</a>
                <a href="/careers" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('careers') ? 'text-blue-600 font-semibold' : '' }}">Careers</a>
                <a href="/blog" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('blog') ? 'text-blue-600 font-semibold' : '' }}">Blog</a>
                <a href="/faq" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('faq') ? 'text-blue-600 font-semibold' : '' }}">FAQ</a>
                <a href="/contact" class="text-gray-700 hover:text-blue-600 transition {{ request()->is('contact') ? 'text-blue-600 font-semibold' : '' }}">Contact</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search..." 
                        value="{{ request('q') }}"
                        class="w-48 px-4 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            <div class="flex items-center space-x-4">
                {{-- Public marketing shell should not expose auth controls --}}
                <a href="https://crm.pradytecai.com" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">Open CRM</a>
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-4 py-4 space-y-3">
                <form action="{{ route('search') }}" method="GET" class="mb-4">
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Search..." 
                        value="{{ request('q') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </form>
                <a href="/" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('/') ? 'text-blue-600 font-semibold' : '' }}">Home</a>
                <a href="/about" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('about') ? 'text-blue-600 font-semibold' : '' }}">About</a>
                <a href="/services" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('services') ? 'text-blue-600 font-semibold' : '' }}">Services</a>
                <a href="/products" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('products') ? 'text-blue-600 font-semibold' : '' }}">Products</a>
                <a href="/careers" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('careers') ? 'text-blue-600 font-semibold' : '' }}">Careers</a>
                <a href="/blog" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('blog') ? 'text-blue-600 font-semibold' : '' }}">Blog</a>
                <a href="/faq" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('faq') ? 'text-blue-600 font-semibold' : '' }}">FAQ</a>
                <a href="/contact" class="block text-gray-700 hover:text-blue-600 transition {{ request()->is('contact') ? 'text-blue-600 font-semibold' : '' }}">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-slate-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-white text-xl font-semibold tracking-tight mb-3">Pradytecai</h3>
                <p class="text-sm leading-relaxed">
                    Enterprise software solutions for modern businesses. Secure, scalable and built for growth.
                </p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wide">Solutions</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="https://crm.pradytecai.com" target="_blank" class="hover:text-white hover:underline underline-offset-4 transition">BulkSMS CRM</a></li>
                    <li><a href="https://demo.pradytec.com" target="_blank" class="hover:text-white hover:underline underline-offset-4 transition">Prady Mfi</a></li>
                    <li><a href="/products" class="hover:text-white hover:underline underline-offset-4 transition">All Products</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wide">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/about" class="hover:text-white hover:underline underline-offset-4 transition">About Us</a></li>
                    <li><a href="/services" class="hover:text-white hover:underline underline-offset-4 transition">Services</a></li>
                    <li><a href="/careers" class="hover:text-white hover:underline underline-offset-4 transition">Careers</a></li>
                    <li><a href="/blog" class="hover:text-white hover:underline underline-offset-4 transition">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm uppercase tracking-wide">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/faq" class="hover:text-white hover:underline underline-offset-4 transition">FAQ</a></li>
                    <li><a href="/contact" class="hover:text-white hover:underline underline-offset-4 transition">Contact</a></li>
                    <li><a href="/policies" class="hover:text-white hover:underline underline-offset-4 transition">Terms & Privacy</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="order-1 w-full md:w-auto flex items-center justify-start gap-4">
                <span class="text-sm font-medium text-slate-200 hidden sm:inline-block">Follow Us</span>
                <div class="flex items-center gap-3">
                    <a href="https://www.linkedin.com" target="_blank" rel="noopener" aria-label="Pradytecai on LinkedIn" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-700 text-slate-200 hover:text-white transition-all transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4.98 3.5C3.88 3.5 3 4.38 3 5.47c0 1.06.86 1.93 1.96 1.93h.02c1.1 0 1.98-.87 1.98-1.93C6.96 4.38 6.08 3.5 4.98 3.5zM3.25 8.75h3.47V20.5H3.25V8.75zM9.5 8.75h3.32v1.6h.05c.46-.87 1.57-1.78 3.23-1.78 3.45 0 4.09 2.27 4.09 5.22v6.71h-3.47v-5.95c0-1.42-.03-3.24-1.98-3.24-1.98 0-2.29 1.54-2.29 3.14v6.05H9.5V8.75z" />
                        </svg>
                    </a>
                    <a href="https://x.com" target="_blank" rel="noopener" aria-label="Pradytecai on X" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-700 text-slate-200 hover:text-white transition-all transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 4h3.039l-6.64 7.59L22 20h-3.18l-3.92-4.6-3.52 4.6H8.34l6.64-7.59L6 4h3.18l3.42 4.01L16.48 4z" />
                        </svg>
                    </a>
                    <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Pradytecai on Facebook" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-700 text-slate-200 hover:text-white transition-all transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13 10h2.5l-.3 3H13v7h-3v-7H8v-3h2V8.5C10 6.57 11.57 5 13.5 5H17v3h-2c-.55 0-1 .45-1 1V10z" />
                        </svg>
                    </a>
                    <a href="https://www.youtube.com" target="_blank" rel="noopener" aria-label="Pradytecai on YouTube" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-700 text-slate-200 hover:text-white transition-all transform hover:scale-110">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21.8 8.3a2.7 2.7 0 0 0-1.9-1.9C18.4 6 12 6 12 6s-6.4 0-7.9.4a2.7 2.7 0 0 0-1.9 1.9C2 9.8 2 12 2 12s0 2.2.2 3.7a2.7 2.7 0 0 0 1.9 1.9C5.6 18 12 18 12 18s6.4 0 7.9-.4a2.7 2.7 0 0 0 1.9-1.9C22 14.2 22 12 22 12s0-2.2-.2-3.7zM10 15.5v-7l6 3.5-6 3.5z" />
                        </svg>
                    </a>
                </div>
            </div>
            <p class="order-2 text-center md:text-right w-full md:w-auto">
                &copy; {{ date('Y') }} Pradytecai. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    <div id="chatbot-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 10001;">
        <!-- Chat panel -->
        <div id="chatbot-panel" class="hidden w-[360px] max-w-[90vw] bg-white/95 backdrop-blur border border-slate-200 rounded-2xl shadow-2xl overflow-hidden mb-3">
            <div class="bg-gradient-to-r from-indigo-600 to-sky-600 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-white/10 border border-white/30 flex items-center justify-center text-xs font-semibold">
                        PT
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-sm">Pradytecai Assistant</p>
                        <p class="text-[11px] text-indigo-100">Online • Typically replies in a few minutes</p>
                    </div>
                </div>
                <button id="chatbot-close" type="button" class="text-indigo-100 hover:text-white text-lg font-bold px-2 py-1" style="cursor: pointer;">
                    ✕
                </button>
            </div>
            <div id="chatbot-messages" class="px-4 py-3 space-y-3 max-h-72 overflow-y-auto text-sm bg-slate-50">
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-semibold">
                        PT
                    </div>
                    <div class="bg-white border border-indigo-100 rounded-2xl px-3 py-2 shadow-sm">
                        <p class="text-gray-800 font-medium mb-1">Hi, welcome to Pradytecai.</p>
                        <p class="text-gray-700">Tell us briefly what you need and we'll route it to the right team.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">
                                BulkSMS CRM support
                            </button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">
                                Prady Mfi demo
                            </button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">
                                Talk to sales
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="chatbot-form" class="border-t border-slate-200 bg-white/95 flex items-center px-3 py-2 space-x-2">
                <input
                    id="chatbot-input"
                    type="text"
                    placeholder="Type your message..."
                    class="flex-1 text-sm px-3 py-2 rounded-full border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-3 py-2 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition"
                    style="cursor: pointer;"
                >
                    Send
                </button>
            </form>
        </div>

        <!-- Chat trigger button -->
        <button
            id="chatbot-toggle"
            type="button"
            class="rounded-full shadow-xl bg-gradient-to-br from-indigo-600 to-sky-600 text-white flex items-center justify-center w-14 h-14 hover:from-indigo-500 hover:to-sky-500 focus:outline-none focus:ring-2 focus:ring-indigo-300"
            aria-label="Open chat support"
            style="cursor: pointer; border: none; outline: none;"
        >
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 10c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 18l1.395-3.72C3.512 12.042 3 10.574 3 9c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>
    </div>

    <!-- Scripts -->
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href || href === '#') return;
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Chatbot functionality - simplified and reliable
            (function initChatbot() {
                const toggleBtn = document.getElementById('chatbot-toggle');
                const panel = document.getElementById('chatbot-panel');
                const closeBtn = document.getElementById('chatbot-close');
                const form = document.getElementById('chatbot-form');
                const input = document.getElementById('chatbot-input');
                const messages = document.getElementById('chatbot-messages');
                const quickButtons = document.querySelectorAll('.chatbot-quick');

                if (!toggleBtn || !panel) {
                    console.error('Chatbot: Required elements missing');
                    return;
                }

                // Toggle functions
                function openChat() {
                    panel.classList.remove('hidden');
                    if (input) setTimeout(() => input.focus(), 150);
                }

                function closeChat() {
                    panel.classList.add('hidden');
                }

                function toggleChat() {
                    if (panel.classList.contains('hidden')) {
                        openChat();
                    } else {
                        closeChat();
                    }
                }

                // Make globally available
                window.toggleChatbot = toggleChat;

                // Button click handler - multiple approaches for reliability
                function handleToggleClick(e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    toggleChat();
                }

                toggleBtn.onclick = handleToggleClick;
                toggleBtn.addEventListener('click', handleToggleClick, true);
                toggleBtn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    handleToggleClick(e);
                }, true);

                // Close button
                if (closeBtn) {
                    closeBtn.onclick = function(e) {
                        e.preventDefault();
                        closeChat();
                    };
                }

                // Form submission
                if (form && input && messages) {
                    form.onsubmit = function(e) {
                        e.preventDefault();
                        const text = input.value.trim();
                        if (!text) return false;

                        const userMsg = document.createElement('div');
                        userMsg.className = 'flex items-start justify-end space-x-2';
                        userMsg.innerHTML = '<div class="bg-indigo-600 text-white rounded-2xl px-3 py-2 text-sm max-w-[80%] shadow-sm">' + 
                            text.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                        messages.appendChild(userMsg);
                        messages.scrollTop = messages.scrollHeight;
                        input.value = '';

                        setTimeout(function() {
                            const botReply = document.createElement('div');
                            botReply.className = 'flex items-start space-x-2 mt-2';
                            botReply.innerHTML = 
                                '<div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-semibold">PT</div>' +
                                '<div class="bg-white border border-indigo-100 rounded-2xl px-3 py-2 shadow-sm text-sm max-w-[80%]">' +
                                '<p class="text-gray-800 mb-1">Thanks for your message.</p>' +
                                '<p class="text-gray-700">We\'ve logged your request. A member of the Pradytecai team will follow up via email or phone.</p>' +
                                '</div>';
                            messages.appendChild(botReply);
                            messages.scrollTop = messages.scrollHeight;
                        }, 600);

                        return false;
                    };
                }

                // Quick reply buttons
                quickButtons.forEach(function(btn) {
                    btn.onclick = function() {
                        const text = this.textContent.trim();
                        if (!text) return;
                        openChat();
                        if (messages) {
                            const userMsg = document.createElement('div');
                            userMsg.className = 'flex items-start justify-end space-x-2';
                            userMsg.innerHTML = '<div class="bg-indigo-600 text-white rounded-2xl px-3 py-2 text-sm max-w-[80%] shadow-sm">' + 
                                text.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                            messages.appendChild(userMsg);
                            messages.scrollTop = messages.scrollHeight;

                            setTimeout(function() {
                                const botReply = document.createElement('div');
                                botReply.className = 'flex items-start space-x-2 mt-2';
                                botReply.innerHTML = 
                                    '<div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-semibold">PT</div>' +
                                    '<div class="bg-white border border-indigo-100 rounded-2xl px-3 py-2 shadow-sm text-sm max-w-[80%]">' +
                                    '<p class="text-gray-800 mb-1">Thanks for your message.</p>' +
                                    '<p class="text-gray-700">We\'ve logged your request. A member of the Pradytecai team will follow up via email or phone.</p>' +
                                    '</div>';
                                messages.appendChild(botReply);
                                messages.scrollTop = messages.scrollHeight;
                            }, 600);
                        }
                    };
                });
            })();
        });

        // Lazy loading images
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for browsers without IntersectionObserver
                lazyImages.forEach(function(img) {
                    img.classList.add('loaded');
                });
            }
        });
    </script>
    @stack('scripts')
    
    <!-- Back to Top Button -->
    @include('components.back-to-top')
    
    <!-- Cookie Consent Banner -->
    @include('components.cookie-consent')
</body>
</html>
