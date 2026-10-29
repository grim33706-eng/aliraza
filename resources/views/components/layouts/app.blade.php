@props([
    'title' => null,
    'description' => config('portfolio.profile.summary'),
    'page' => 'page',
])
@php
    $profile = config('portfolio.profile');
    $fullTitle = $title ? $title.' · '.$profile['name'] : $profile['name'].' — '.$profile['title'];
    // Curtain label matches the navigation wording for each page.
    $curtainLabel = ['home' => 'Home', 'about' => 'About', 'services' => 'Services', 'portfolio' => 'Portfolio', 'results' => 'Results', 'international' => 'International', 'contact' => 'Contact'][request()->route()?->getName()] ?? $title ?? 'Home';
@endphp
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Applies the saved theme and JS flags before first paint, so there is no flash. --}}
    <script>
        (function (d) {
            var h = d.documentElement;
            h.classList.replace('no-js', 'js');
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) h.classList.add('reduced-motion');
            // Safety net: if the animation bundle never boots, show everything.
            setTimeout(function () { if (!h.classList.contains('motion-ready')) h.classList.add('reveal-fallback'); }, 3000);
            try {
                var t = localStorage.getItem('raza-theme');
                h.setAttribute('data-theme', t || (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'));
                if (sessionStorage.getItem('raza-transition')) { h.classList.add('is-entering'); sessionStorage.removeItem('raza-transition'); }
            } catch (e) { h.setAttribute('data-theme', 'dark'); }
        })(document);
    </script>

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="{{ $profile['name'] }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#08090f">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $profile['name'] }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body data-page="{{ $page }}">
    <a href="#main" class="skip-link">Skip to content</a>
    {{-- Page-transition curtain. The label shows this page's name; on leaving, JS swaps in the destination's. --}}
    <div class="page-curtain" aria-hidden="true">
        <div class="page-curtain__inner">
            <span class="page-curtain__logo"><span class="page-curtain__ring"></span><span class="brand__mark">RS</span></span>
            <span class="page-curtain__mask"><span class="page-curtain__label" data-curtain-label>{{ $curtainLabel }}</span></span>
            <span class="page-curtain__bar"><i data-curtain-bar></i></span>
            <span class="page-curtain__meta">Raza Sandhu · Social Media &amp; Performance Marketing</span>
        </div>
    </div>
    <div class="grain" aria-hidden="true"></div>

    @include('partials.nav')

    <main id="main" tabindex="-1">
        {{ $slot }}
    </main>

    @include('partials.footer')

    @include('partials.social-dock')

    @stack('scripts')
</body>
</html>
