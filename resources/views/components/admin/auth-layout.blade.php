@props(['title'])
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
<body class="ad-body ad-auth">
    <div class="ad-auth__art" aria-hidden="true">
        <div class="ad-auth__glow"></div>
        <div class="ad-auth__grid"></div>
        <div class="ad-auth__orbit"><span></span><span></span><span></span></div>
        <div class="ad-auth__copy">
            <span class="brand__mark">RS</span>
            <p class="ad-auth__kicker">Site admin</p>
            <h2>Edit every page, image and figure of <em>{{ config('portfolio.profile.name') }}</em>'s portfolio.</h2>
            <ul>
                <li><x-icon name="pencil" :size="16" /> Text, headings and buttons on every section</li>
                <li><x-icon name="image" :size="16" /> Photos, screenshots and recordings</li>
                <li><x-icon name="inbox" :size="16" /> Messages from the contact form</li>
            </ul>
        </div>
    </div>

    <main class="ad-auth__panel">
        <div class="ad-auth__card">
            {{ $slot }}
        </div>
    </main>
    <x-admin.toasts :inline-errors="true" />
</body>
</html>
