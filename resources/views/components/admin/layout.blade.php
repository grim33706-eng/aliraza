@props(['title' => 'Dashboard', 'heading' => null, 'subheading' => null])
@php
    $user = auth()->user();
    $currentPage = request()->route('page');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <script>
        (function (h) {
            try { h.setAttribute('data-theme', localStorage.getItem('raza-theme') || (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark')); }
            catch (e) { h.setAttribute('data-theme', 'dark'); }
        })(document.documentElement);
    </script>
    <title>{{ $title }} · Admin · {{ config('portfolio.profile.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap">
    @vite(['resources/scss/admin.scss', 'resources/js/admin.js'])
</head>
<body class="ad-body">
    <a href="#ad-main" class="ad-skip">Skip to content</a>

    <div class="ad-shell">
        {{-- ============ SIDEBAR ============ --}}
        <aside class="ad-sidebar" id="ad-sidebar" data-sidebar>
            <a href="{{ route('admin.dashboard') }}" class="ad-brand">
                <span class="brand__mark">RS</span>
                <span class="ad-brand__text"><strong>{{ config('portfolio.profile.name') }}</strong><small>Site admin</small></span>
            </a>

            <nav class="ad-nav" aria-label="Admin">
                <p class="ad-nav__label">Overview</p>
                <a href="{{ route('admin.dashboard') }}" @class(['ad-nav__link', 'is-active' => request()->routeIs('admin.dashboard')])>
                    <x-icon name="grid" :size="18" /> Dashboard
                </a>

                <p class="ad-nav__label">Website pages</p>
                @foreach (config('admin.pages') as $slug => $page)
                    <a href="{{ route('admin.pages.edit', $slug) }}" @class(['ad-nav__link', 'is-active' => $currentPage === $slug])>
                        <x-icon :name="$page['icon']" :size="18" /> {{ $page['label'] }}
                    </a>
                @endforeach

                <p class="ad-nav__label">Inbox</p>
                <a href="{{ route('admin.messages.index') }}" @class(['ad-nav__link', 'is-active' => request()->routeIs('admin.messages.*')])>
                    <x-icon name="inbox" :size="18" /> Messages
                    @if ($unreadMessages)<span class="ad-badge">{{ $unreadMessages }}</span>@endif
                </a>

                <p class="ad-nav__label">Account</p>
                <a href="{{ route('admin.settings') }}" @class(['ad-nav__link', 'is-active' => request()->routeIs('admin.settings')])>
                    <x-icon name="settings" :size="18" /> Settings
                </a>
            </nav>

            <div class="ad-sidebar__user">
                <x-admin.avatar :user="$user" />
                <div class="ad-sidebar__who">
                    <strong>{{ $user->name }}</strong>
                    <span>{{ $user->email }}</span>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="ad-icon-btn" aria-label="Sign out" title="Sign out"><x-icon name="logout" :size="18" /></button>
                </form>
            </div>
        </aside>
        <div class="ad-scrim" data-sidebar-close></div>

        {{-- ============ MAIN ============ --}}
        <div class="ad-main">
            <header class="ad-topbar">
                <button type="button" class="ad-icon-btn ad-topbar__menu" data-sidebar-toggle aria-controls="ad-sidebar" aria-expanded="false" aria-label="Open navigation">
                    <x-icon name="menu" :size="20" />
                </button>
                <div class="ad-topbar__title">
                    <span class="ad-topbar__crumb">Admin</span>
                    <x-icon name="chevron-right" :size="14" />
                    <span>{{ $title }}</span>
                </div>
                <div class="ad-topbar__actions">
                    <a href="{{ route('home') }}" class="ad-btn ad-btn--ghost ad-btn--sm" target="_blank" rel="noopener"><x-icon name="external" :size="16" /> <span>View website</span></a>
                    <button type="button" class="ad-icon-btn" data-theme-toggle aria-label="Switch colour theme">
                        <x-icon name="sun" :size="18" class="theme-toggle__sun" />
                        <x-icon name="moon" :size="18" class="theme-toggle__moon" />
                    </button>
                    <details class="ad-menu">
                        <summary aria-label="Account menu"><x-admin.avatar :user="$user" size="sm" /></summary>
                        <div class="ad-menu__panel">
                            <div class="ad-menu__who"><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></div>
                            <a href="{{ route('admin.settings') }}"><x-icon name="settings" :size="16" /> Settings</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit"><x-icon name="logout" :size="16" /> Sign out</button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>

            <x-admin.toasts />

            <main class="ad-content" id="ad-main" tabindex="-1">
                @if ($heading)
                    <header class="ad-page-head">
                        <div>
                            <h1>{{ $heading }}</h1>
                            @if ($subheading)<p>{{ $subheading }}</p>@endif
                        </div>
                        @isset($actions)<div class="ad-page-head__actions">{{ $actions }}</div>@endisset
                    </header>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
