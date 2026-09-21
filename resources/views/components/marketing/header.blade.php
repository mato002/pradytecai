@php
    $products = collect(config('portfolio.products', []))->keyBy('slug');
    $groups = config('portfolio.product_groups', []);
    $capabilities = config('portfolio.capabilities', []);
    $industries = config('portfolio.industries', []);
@endphp

<header class="mkt-header">
    <div class="mkt-container">
        <div class="mkt-header__bar">
            <x-prady-logo variant="nav" class="mkt-header__logo" />

            <nav class="mkt-nav" aria-label="Primary">
                <a href="/" class="mkt-nav__link {{ request()->is('/') ? 'is-active' : '' }}">Home</a>

                <div class="mkt-nav__item">
                    <button type="button" class="mkt-nav__trigger" aria-expanded="false" aria-haspopup="true" data-mega-trigger>
                        Products
                        <svg class="mkt-nav__caret" width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="mkt-mega" hidden>
                        <div class="mkt-mega__grid">
                            @foreach($groups as $group)
                                <div>
                                    <p class="mkt-mega__heading">{{ $group['title'] }}</p>
                                    <ul class="mkt-mega__list">
                                        @foreach($group['slugs'] as $slug)
                                            @if($product = $products->get($slug))
                                                <li><a href="/products#{{ $product['slug'] }}">{{ $product['name'] }}</a></li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                        <a href="/products" class="mkt-mega__footer">View All Products →</a>
                    </div>
                </div>

                <div class="mkt-nav__item">
                    <button type="button" class="mkt-nav__trigger" aria-expanded="false" aria-haspopup="true" data-mega-trigger>
                        Solutions
                        <svg class="mkt-nav__caret" width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="mkt-mega mkt-mega--narrow" hidden>
                        <ul class="mkt-mega__list">
                            @foreach($capabilities as $item)
                                <li><a href="{{ $item['href'] }}">{{ $item['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="mkt-nav__item">
                    <button type="button" class="mkt-nav__trigger" aria-expanded="false" aria-haspopup="true" data-mega-trigger>
                        Industries
                        <svg class="mkt-nav__caret" width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="mkt-mega mkt-mega--narrow" hidden>
                        <ul class="mkt-mega__list">
                            @foreach($industries as $item)
                                <li><a href="{{ $item['href'] }}">{{ $item['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

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

            <details class="mkt-mobile-acc">
                <summary>Products</summary>
                <div class="mkt-mobile-acc__body">
                    @foreach($groups as $group)
                        <p class="mkt-mobile-acc__heading">{{ $group['title'] }}</p>
                        @foreach($group['slugs'] as $slug)
                            @if($product = $products->get($slug))
                                <a href="/products#{{ $product['slug'] }}">{{ $product['name'] }}</a>
                            @endif
                        @endforeach
                    @endforeach
                    <a href="/products" class="font-semibold">View All Products →</a>
                </div>
            </details>

            <details class="mkt-mobile-acc">
                <summary>Solutions</summary>
                <div class="mkt-mobile-acc__body">
                    @foreach($capabilities as $item)
                        <a href="{{ $item['href'] }}">{{ $item['name'] }}</a>
                    @endforeach
                </div>
            </details>

            <details class="mkt-mobile-acc">
                <summary>Industries</summary>
                <div class="mkt-mobile-acc__body">
                    @foreach($industries as $item)
                        <a href="{{ $item['href'] }}">{{ $item['name'] }}</a>
                    @endforeach
                </div>
            </details>

            <a href="/about" class="mkt-mobile-menu__link">About</a>
            <a href="/careers" class="mkt-mobile-menu__link">Careers</a>
            <a href="/blog" class="mkt-mobile-menu__link">Blog</a>
            <a href="/contact" class="mkt-mobile-menu__link">Contact</a>
            <a href="/contact" class="mkt-btn mkt-btn--primary mkt-mobile-menu__cta">Get Demo</a>
        </nav>
    </div>
</header>
