@php
    $contact = config('portfolio.contact');
    $footer = config('portfolio.pages.footer');
    $name = config('portfolio.profile.name');
@endphp
<footer class="site-footer">
    <div class="container">
        @unless (request()->routeIs('contact'))
            <section class="cta-panel" data-reveal>
                <div class="cta-panel__scene" data-scene="tunnel" data-lazy aria-hidden="true"><canvas></canvas></div>
                <div class="cta-panel__content">
                    <p class="eyebrow"><span class="pulse" aria-hidden="true"></span>{{ $footer['cta']['eyebrow'] }}</p>
                    <h2 class="cta-panel__title"><x-rich :text="$footer['cta']['title']" /></h2>
                    <p class="cta-panel__lead">{{ $footer['cta']['lead'] }}</p>
                    <div class="hero-actions">
                        <a href="{{ route('contact') }}" class="btn btn--primary btn--lg" data-magnetic>{{ $footer['cta']['primary_label'] }} <x-icon name="arrow-right" /></a>
                        @if ($contact['whatsapp'])
                            <a href="{{ $contact['whatsapp'] }}" class="btn btn--ghost btn--lg" target="_blank" rel="noopener"><x-icon name="whatsapp" /> {{ $footer['cta']['secondary_label'] }}</a>
                        @endif
                    </div>
                </div>
            </section>
        @endunless

        <div class="row g-4 footer-grid">
            <div class="col-lg-5">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand__mark">RS</span>
                    <span class="brand__name">{{ $name }}</span>
                </a>
                <p class="footer-tag">{{ $footer['meta']['tagline'] }}</p>
                <div class="social-row">
                    @foreach (config('portfolio.social') as $social)
                        <a href="{{ $social['url'] }}" class="icon-btn" target="_blank" rel="noopener" aria-label="{{ $social['name'] }}"><x-icon :name="$social['icon']" /></a>
                    @endforeach
                    @if ($contact['whatsapp'])
                        <a href="{{ $contact['whatsapp'] }}" class="icon-btn" target="_blank" rel="noopener" aria-label="WhatsApp"><x-icon name="whatsapp" /></a>
                    @endif
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <h3 class="footer-head">Explore</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('services') }}">Services</a></li>
                    <li><a href="{{ route('portfolio') }}">Portfolio</a></li>
                    <li><a href="{{ route('results') }}">Results</a></li>
                    <li><a href="{{ route('international') }}">International</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h3 class="footer-head">Get in touch</h3>
                <ul class="footer-links">
                    <li><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
                    <li><a href="tel:{{ $contact['phone_e164'] }}">{{ $contact['phone_display'] }}</a></li>
                    <li>{{ config('portfolio.profile.location') }}</li>
                </ul>
            </div>
        </div>

        <p class="footer-wordmark" aria-hidden="true" data-wordmark>{{ $name }}</p>

        <div class="footer-bottom">
            <p><x-rich :text="$footer['meta']['copyright']" /></p>
            <a href="#main" class="footer-top-link" data-scroll-top>Back to top <x-icon name="arrow-up" :size="16" /></a>
        </div>
    </div>
</footer>
