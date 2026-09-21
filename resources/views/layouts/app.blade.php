<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prady Technologies | Smart Technology Solutions for African Businesses')</title>
    <meta name="description" content="@yield('description', 'Prady Technologies builds secure, efficient software solutions for lenders, SACCOs, businesses, property managers, mobility and digital commerce.')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-animate {
            opacity: 0;
            transform: translateY(18px);
            animation: heroFadeUp 0.65s ease-out forwards;
        }
        .hero-animate.delay-md { animation-delay: 0.15s; }
    </style>
    @stack('styles')
</head>
<body class="prady-site antialiased">
@php
    $contact = config('portfolio.contact', []);
    $footerProducts = array_slice(config('portfolio.products', []), 0, 6);
@endphp

    <header class="mkt-header">
        <div class="mkt-container">
            <div class="mkt-header__bar">
                <x-prady-logo variant="nav" />

                <nav class="mkt-nav" aria-label="Primary">
                    <a href="/" class="mkt-nav__link {{ request()->is('/') ? 'is-active' : '' }}">Home</a>
                    <a href="/#solutions" class="mkt-nav__link">Solutions</a>
                    <a href="/products" class="mkt-nav__link {{ request()->is('products') ? 'is-active' : '' }}">Products</a>
                    <a href="/services" class="mkt-nav__link {{ request()->is('services') ? 'is-active' : '' }}">Industries</a>
                    <a href="/about" class="mkt-nav__link {{ request()->is('about') ? 'is-active' : '' }}">About</a>
                    <a href="/contact" class="mkt-nav__link {{ request()->is('contact') ? 'is-active' : '' }}">Contact</a>
                </nav>

                <div class="mkt-header__actions">
                    <a href="/contact" class="mkt-btn mkt-btn--primary mkt-header__cta">Get Demo</a>
                    <button
                        id="mobile-menu-button"
                        class="mkt-menu-toggle"
                        type="button"
                        aria-label="Open menu"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="mkt-mobile-menu hidden" hidden>
            <nav class="mkt-mobile-menu__inner" aria-label="Mobile">
                <a href="/" class="mkt-mobile-menu__link">Home</a>
                <a href="/#solutions" class="mkt-mobile-menu__link">Solutions</a>
                <a href="/products" class="mkt-mobile-menu__link">Products</a>
                <a href="/services" class="mkt-mobile-menu__link">Industries</a>
                <a href="/about" class="mkt-mobile-menu__link">About</a>
                <a href="/careers" class="mkt-mobile-menu__link">Careers</a>
                <a href="/blog" class="mkt-mobile-menu__link">Blog</a>
                <a href="/contact" class="mkt-mobile-menu__link">Contact</a>
                <a href="/contact" class="mkt-btn mkt-btn--primary mkt-mobile-menu__cta">Get Demo</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mkt-footer">
        <div class="mkt-container">
            <div class="mkt-footer__grid">
                <div class="mkt-footer__brand">
                    <x-prady-logo variant="footer" theme="dark" />
                    <p class="mkt-footer__statement">
                        Smart technology solutions for ambitious businesses. We build secure, efficient software that drives growth.
                    </p>
                </div>

                <div>
                    <h3 class="mkt-footer__heading">Company</h3>
                    <ul class="mkt-footer__list">
                        <li><a href="/about">About</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/services">Industries</a></li>
                        <li><a href="/careers">Careers</a></li>
                        <li><a href="/blog">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="mkt-footer__heading">Solutions / Products</h3>
                    <ul class="mkt-footer__list">
                        @foreach($footerProducts as $item)
                            <li><a href="/products#{{ $item['slug'] }}">{{ $item['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="mkt-footer__heading">Contact</h3>
                    <ul class="mkt-footer__list mkt-footer__contact">
                        @if(!empty($contact['phone']))
                            <li>
                                <span>Phone</span>
                                <a href="{{ $contact['phone_href'] ?? '#' }}">{{ $contact['phone'] }}</a>
                            </li>
                        @endif
                        @if(!empty($contact['email']))
                            <li>
                                <span>Email</span>
                                <a href="{{ $contact['email_href'] ?? '#' }}">{{ $contact['email'] }}</a>
                            </li>
                        @endif
                        @if(!empty($contact['location']))
                            <li>
                                <span>Location</span>
                                <span class="mkt-footer__muted">{{ $contact['location'] }}</span>
                            </li>
                        @endif
                        <li><a href="/policies">Terms &amp; Privacy</a></li>
                    </ul>
                </div>
            </div>

            <div class="mkt-footer__bottom">
                <p>&copy; {{ date('Y') }} Prady Technologies Ltd. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot Widget -->
    <div id="chatbot-container" style="position: fixed; bottom: 24px; right: 24px; z-index: 10001;">
        <div id="chatbot-panel" class="hidden w-[360px] max-w-[90vw] bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden mb-3">
            <div class="text-white px-4 py-3 flex items-center justify-between" style="background: linear-gradient(110deg, #00398C 0%, #0557A6 48%, #0487CD 100%);">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-white/10 border border-white/30 flex items-center justify-center overflow-hidden">
                        <svg width="22" height="22" viewBox="0 0 48 48" fill="none"><path d="M6 6h28v16H22v8H6V6z" fill="#fff"/><path d="M22 22h16v16H22V22z" fill="#fff"/><rect x="28" y="28" width="14" height="14" fill="#19A7EF"/></svg>
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
                    <div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-xs font-semibold" style="background:#053171;">PT</div>
                    <div class="bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm">
                        <p class="text-gray-800 font-medium mb-1">Hi, welcome to Prady Technologies.</p>
                        <p class="text-gray-700">Tell us briefly what you need and we'll route it to the right team.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">Microfinance demo</button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">SACCO System</button>
                            <button type="button" class="chatbot-quick text-[11px] px-3 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200" style="cursor: pointer;">Talk to sales</button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="chatbot-form" class="border-t border-slate-200 bg-white flex items-center px-3 py-2 space-x-2">
                <input id="chatbot-input" type="text" placeholder="Type your message..." class="flex-1 text-sm px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#19A7EF] focus:border-transparent" autocomplete="off" />
                <button type="submit" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-white text-xs font-semibold hover:opacity-90 transition" style="cursor: pointer; background:#0A4E99; border:none;">Send</button>
            </form>
        </div>

        <button id="chatbot-toggle" type="button" class="rounded-full shadow-xl text-white flex items-center justify-center w-14 h-14 focus:outline-none focus:ring-2 focus:ring-sky-300" aria-label="Open chat support" style="cursor: pointer; border: none; outline: none; background: linear-gradient(135deg, #053171, #0487CD);">
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
                    const isOpen = !mobileMenu.classList.contains('hidden');
                    mobileMenu.classList.toggle('hidden', isOpen);
                    mobileMenu.hidden = isOpen;
                    mobileMenuButton.setAttribute('aria-expanded', String(!isOpen));
                    mobileMenuButton.setAttribute('aria-label', isOpen ? 'Open menu' : 'Close menu');
                });

                mobileMenu.querySelectorAll('a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.hidden = true;
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        mobileMenuButton.setAttribute('aria-label', 'Open menu');
                    });
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
                    userMsg.innerHTML = '<div class="text-white rounded-xl px-3 py-2 text-sm max-w-[80%] shadow-sm" style="background:#053171;">' + text.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                    messages.appendChild(userMsg);
                    messages.scrollTop = messages.scrollHeight;
                }

                function appendBot() {
                    setTimeout(function() {
                        const botReply = document.createElement('div');
                        botReply.className = 'flex items-start space-x-2 mt-2';
                        botReply.innerHTML =
                            '<div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-xs font-semibold" style="background:#053171;">PT</div>' +
                            '<div class="bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm text-sm max-w-[80%]">' +
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
