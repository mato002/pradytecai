<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prady Technologies Ltd — Doing It Differently')</title>
    <meta name="description" content="@yield('description', 'Prady Technologies Ltd builds secure, efficient software that drives growth across finance, mobility, property, and commerce.')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif; }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-animate {
            opacity: 0;
            transform: translateY(24px);
            animation: heroFadeUp 0.8s ease-out forwards;
        }
        .hero-animate.delay-sm { animation-delay: 0.1s; }
        .hero-animate.delay-md { animation-delay: 0.2s; }
        .hero-animate.delay-lg { animation-delay: 0.3s; }
    </style>
    @stack('styles')
</head>
<body class="prady-site text-prady-navy antialiased">
    <nav class="prady-nav fixed top-0 left-0 right-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-[4.5rem] items-center justify-between gap-4">
                <x-prady-logo variant="nav" />

                <div class="hidden lg:flex items-center gap-7">
                    <a href="/" class="prady-nav-link {{ request()->is('/') ? 'is-active' : '' }}">Home</a>
                    <a href="/#solutions" class="prady-nav-link">Solutions</a>
                    <a href="/products" class="prady-nav-link {{ request()->is('products') ? 'is-active' : '' }}">Products</a>
                    <a href="/services" class="prady-nav-link {{ request()->is('services') ? 'is-active' : '' }}">Industries</a>
                    <a href="/about" class="prady-nav-link {{ request()->is('about') ? 'is-active' : '' }}">About</a>
                    <a href="/contact" class="prady-nav-link {{ request()->is('contact') ? 'is-active' : '' }}">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="/contact" class="prady-btn-primary hidden sm:inline-flex">Get Demo</a>
                    <button id="mobile-menu-button" class="lg:hidden p-2 rounded-lg text-[#0B2347] hover:bg-[#E8F6FC]" aria-label="Open menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-100 bg-white">
            <div class="px-4 py-4 space-y-1">
                <a href="/" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Home</a>
                <a href="/#solutions" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Solutions</a>
                <a href="/products" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Products</a>
                <a href="/services" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Industries</a>
                <a href="/about" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">About</a>
                <a href="/careers" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Careers</a>
                <a href="/blog" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Blog</a>
                <a href="/contact" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-[#0B2347] hover:bg-[#E8F6FC]">Contact</a>
                <a href="/contact" class="prady-btn-primary w-full mt-2">Get Demo</a>
            </div>
        </div>
    </nav>

    <main class="pt-[4.5rem]">
        @yield('content')
    </main>

    <footer class="prady-footer pt-14 pb-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="grid md:grid-cols-4 gap-10 mb-10">
                <div class="md:col-span-1">
                    <x-prady-logo variant="footer" theme="dark" class="mb-4" />
                    <p class="text-sm leading-relaxed text-white/70 mt-4">
                        Smart technology solutions for ambitious businesses. We build secure, efficient software that drives growth.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Products</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach(array_slice(config('portfolio.products'), 0, 5) as $item)
                            <li><a href="/products#{{ $item['slug'] }}" class="hover:text-[#00AEEF] transition">{{ $item['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/about" class="hover:text-[#00AEEF] transition">About Us</a></li>
                        <li><a href="/services" class="hover:text-[#00AEEF] transition">Industries</a></li>
                        <li><a href="/careers" class="hover:text-[#00AEEF] transition">Careers</a></li>
                        <li><a href="/blog" class="hover:text-[#00AEEF] transition">Blog</a></li>
                        <li><a href="/faq" class="hover:text-[#00AEEF] transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/contact" class="hover:text-[#00AEEF] transition">Contact</a></li>
                        <li><a href="/policies" class="hover:text-[#00AEEF] transition">Terms & Privacy</a></li>
                        <li><a href="/contact" class="hover:text-[#00AEEF] transition">Request a Demo</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-white/50">
                <div class="flex items-center gap-3">
                    <a href="https://www.linkedin.com" target="_blank" rel="noopener" aria-label="Prady Technologies on LinkedIn" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#00AEEF] text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5C3.88 3.5 3 4.38 3 5.47c0 1.06.86 1.93 1.96 1.93h.02c1.1 0 1.98-.87 1.98-1.93C6.96 4.38 6.08 3.5 4.98 3.5zM3.25 8.75h3.47V20.5H3.25V8.75zM9.5 8.75h3.32v1.6h.05c.46-.87 1.57-1.78 3.23-1.78 3.45 0 4.09 2.27 4.09 5.22v6.71h-3.47v-5.95c0-1.42-.03-3.24-1.98-3.24-1.98 0-2.29 1.54-2.29 3.14v6.05H9.5V8.75z"/></svg>
                    </a>
                    <a href="https://x.com" target="_blank" rel="noopener" aria-label="Prady Technologies on X" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#00AEEF] text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 4h3.039l-6.64 7.59L22 20h-3.18l-3.92-4.6-3.52 4.6H8.34l6.64-7.59L6 4h3.18l3.42 4.01L16.48 4z"/></svg>
                    </a>
                    <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Prady Technologies on Facebook" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/10 hover:bg-[#00AEEF] text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10h2.5l-.3 3H13v7h-3v-7H8v-3h2V8.5C10 6.57 11.57 5 13.5 5H17v3h-2c-.55 0-1 .45-1 1V10z"/></svg>
                    </a>
                </div>
                <p>&copy; {{ date('Y') }} Prady Technologies Ltd. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    <div id="chatbot-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 10001;">
        <div id="chatbot-panel" class="hidden w-[360px] max-w-[90vw] bg-white/95 backdrop-blur border border-slate-200 rounded-2xl shadow-2xl overflow-hidden mb-3">
            <div class="bg-gradient-to-r from-[#0B2347] to-[#1A4B8C] text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-white/10 border border-white/30 flex items-center justify-center overflow-hidden">
                        <svg width="22" height="22" viewBox="0 0 48 48" fill="none"><path d="M6 6h28v16H22v8H6V6z" fill="#fff"/><path d="M22 22h16v16H22V22z" fill="#fff"/><rect x="28" y="28" width="14" height="14" fill="#00AEEF"/></svg>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-sm">Prady Assistant</p>
                        <p class="text-[11px] text-sky-100">Online • Typically replies in a few minutes</p>
                    </div>
                </div>
                <button id="chatbot-close" type="button" class="text-sky-100 hover:text-white text-lg font-bold px-2 py-1" style="cursor: pointer;">✕</button>
            </div>
            <div id="chatbot-messages" class="px-4 py-3 space-y-3 max-h-72 overflow-y-auto text-sm bg-slate-50">
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 rounded-full bg-[#0B2347] text-white flex items-center justify-center text-xs font-semibold">PT</div>
                    <div class="bg-white border border-sky-100 rounded-2xl px-3 py-2 shadow-sm">
                        <p class="text-gray-800 font-medium mb-1">Hi, welcome to Prady Technologies.</p>
                        <p class="text-gray-700">Tell us briefly what you need and we'll route it to the right team.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">Microfinance demo</button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">SACCO System</button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">Talk to sales</button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="chatbot-form" class="border-t border-slate-200 bg-white/95 flex items-center px-3 py-2 space-x-2">
                <input id="chatbot-input" type="text" placeholder="Type your message..." class="flex-1 text-sm px-3 py-2 rounded-full border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#00AEEF] focus:border-transparent" autocomplete="off" />
                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded-full bg-[#0B2347] text-white text-xs font-semibold hover:bg-[#1A4B8C] transition" style="cursor: pointer;">Send</button>
            </form>
        </div>

        <button id="chatbot-toggle" type="button" class="rounded-full shadow-xl bg-gradient-to-br from-[#0B2347] to-[#00AEEF] text-white flex items-center justify-center w-14 h-14 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-sky-300" aria-label="Open chat support" style="cursor: pointer; border: none; outline: none;">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 10c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 18l1.395-3.72C3.512 12.042 3 10.574 3 9c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href || href === '#') return;
                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            (function initChatbot() {
                const toggleBtn = document.getElementById('chatbot-toggle');
                const panel = document.getElementById('chatbot-panel');
                const closeBtn = document.getElementById('chatbot-close');
                const form = document.getElementById('chatbot-form');
                const input = document.getElementById('chatbot-input');
                const messages = document.getElementById('chatbot-messages');
                const quickButtons = document.querySelectorAll('.chatbot-quick');
                if (!toggleBtn || !panel) return;

                function openChat() { panel.classList.remove('hidden'); if (input) setTimeout(() => input.focus(), 150); }
                function closeChat() { panel.classList.add('hidden'); }
                function toggleChat() { panel.classList.contains('hidden') ? openChat() : closeChat(); }

                toggleBtn.addEventListener('click', function(e) { e.preventDefault(); toggleChat(); });
                if (closeBtn) closeBtn.addEventListener('click', function(e) { e.preventDefault(); closeChat(); });

                function appendUser(text) {
                    const userMsg = document.createElement('div');
                    userMsg.className = 'flex items-start justify-end space-x-2';
                    userMsg.innerHTML = '<div class="bg-[#0B2347] text-white rounded-2xl px-3 py-2 text-sm max-w-[80%] shadow-sm">' + text.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                    messages.appendChild(userMsg);
                    messages.scrollTop = messages.scrollHeight;
                }

                function appendBot() {
                    setTimeout(function() {
                        const botReply = document.createElement('div');
                        botReply.className = 'flex items-start space-x-2 mt-2';
                        botReply.innerHTML =
                            '<div class="w-8 h-8 rounded-full bg-[#0B2347] text-white flex items-center justify-center text-xs font-semibold">PT</div>' +
                            '<div class="bg-white border border-sky-100 rounded-2xl px-3 py-2 shadow-sm text-sm max-w-[80%]">' +
                            '<p class="text-gray-800 mb-1">Thanks for your message.</p>' +
                            '<p class="text-gray-700">We\'ve logged your request. A member of the Prady Technologies team will follow up.</p>' +
                            '</div>';
                        messages.appendChild(botReply);
                        messages.scrollTop = messages.scrollHeight;
                    }, 600);
                }

                if (form && input && messages) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const text = input.value.trim();
                        if (!text) return;
                        appendUser(text);
                        input.value = '';
                        appendBot();
                    });
                }

                quickButtons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const text = this.textContent.trim();
                        if (!text) return;
                        openChat();
                        appendUser(text);
                        appendBot();
                    });
                });
            })();
        });
    </script>
    @stack('scripts')
    @include('components.back-to-top')
    @include('components.cookie-consent')
</body>
</html>
