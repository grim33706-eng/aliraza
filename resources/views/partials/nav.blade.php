@php
    $links = [
        'home' => 'Home',
        'about' => 'About',
        'services' => 'Services',
        'portfolio' => 'Portfolio',
        'results' => 'Results',
        'international' => 'International',
    ];
@endphp
<header class="site-nav" data-nav>
    <div class="site-nav__bar">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ config('portfolio.profile.name') }} — home">
            <span class="brand__mark">RS</span>
            <span class="brand__name">{{ config('portfolio.profile.name') }}</span>
        </a>

        <nav class="site-nav__links" aria-label="Primary">
            <ul data-nav-links>
                <li class="nav-indicator" aria-hidden="true" data-nav-indicator></li>
                @foreach ($links as $route => $label)
                    <li>
                        <a href="{{ route($route) }}" @class(['nav-link', 'is-active' => request()->routeIs($route)]) @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="site-nav__actions">
            <button type="button" class="icon-btn theme-toggle" data-theme-toggle aria-label="Switch colour theme">
                <x-icon name="sun" class="theme-toggle__sun" />
                <x-icon name="moon" class="theme-toggle__moon" />
            </button>
            <a href="{{ route('contact') }}" @class(['btn btn--primary btn--sm site-nav__cta', 'is-active' => request()->routeIs('contact')]) @if (request()->routeIs('contact')) aria-current="page" @endif>
                Let's talk <x-icon name="arrow-up-right" :size="16" />
            </a>
            <button type="button" class="icon-btn menu-toggle" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
                <span></span><span></span>
            </button>
        </div>
    </div>
</header>
<div class="scroll-progress" aria-hidden="true"><span data-scroll-progress></span></div>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
    <nav aria-label="Mobile">
        <ul>
            @foreach ($links + ['contact' => 'Contact'] as $route => $label)
                <li>
                    <a href="{{ route($route) }}" data-page-label="{{ $label }}" @class(['mobile-menu__link', 'is-active' => request()->routeIs($route)]) @if (request()->routeIs($route)) aria-current="page" @endif>
                        <span class="mobile-menu__index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}
                        <x-icon name="arrow-up-right" :size="22" class="mobile-menu__arrow" />
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
    <div class="mobile-menu__foot">
        <a href="mailto:{{ config('portfolio.contact.email') }}">{{ config('portfolio.contact.email') }}</a>
        <div class="social-row">
            @foreach (config('portfolio.social') as $social)
                <a href="{{ $social['url'] }}" class="icon-btn" target="_blank" rel="noopener" aria-label="{{ $social['name'] }}"><x-icon :name="$social['icon']" /></a>
            @endforeach
        </div>
    </div>
</div>
