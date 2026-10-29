@php
    $profile = config('portfolio.profile');
    $results = config('portfolio.results');
    $p = config('portfolio.pages.home');
    $tones = ['accent', 'iris', 'coral', 'iris'];
    $depths = [0.6, 1, 0.8];
    $globeData = ['home' => config('portfolio.international.home'), 'markets' => $markets->map(fn ($m) => ['name' => $m['name'], 'lat' => $m['lat'], 'lng' => $m['lng']])->values()];
@endphp
<x-layouts.app page="home" :title="$p['seo']['title'] ?: null" :description="$p['seo']['description'] ?: $profile['summary']">
    @push('head')
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush

    {{-- ============ HERO ============ --}}
    <section class="home-hero" data-hero>
        <div class="hero-scene" data-scene="core" aria-hidden="true"><canvas></canvas></div>
        <div class="hero-glow" aria-hidden="true"></div>
        <div class="hero-grid-lines" aria-hidden="true"></div>

        <div class="container home-hero__inner">
            <div class="home-hero__copy">
                <p class="eyebrow eyebrow--pill" data-hero-item><span class="pulse" aria-hidden="true"></span>{{ $p['hero']['eyebrow'] }}</p>
                <h1 class="home-hero__title" data-split><x-rich :text="$p['hero']['title']" /></h1>
                <p class="home-hero__lead" data-hero-item><x-rich :text="$p['hero']['lead']" /></p>
                <div class="hero-actions" data-hero-item>
                    <a href="{{ route('portfolio') }}" class="btn btn--primary btn--lg" data-magnetic>{{ $p['hero']['primary_label'] }} <x-icon name="arrow-right" /></a>
                    <a href="{{ route('contact') }}" class="btn btn--ghost btn--lg">{{ $p['hero']['secondary_label'] }}</a>
                </div>
                @if ($p['hero']['platforms'])
                    <ul class="hero-platforms" data-hero-item aria-label="Ad platforms">
                        @foreach ($p['hero']['platforms'] as $platform)
                            <li><x-icon :name="$platform['icon']" :size="16" /> {{ $platform['label'] }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="hero-kpis" aria-label="{{ $p['hero']['kpi_note'] }}">
                @foreach ($p['hero']['kpis'] as $kpi)
                    <div @class(['kpi-chip', 'kpi-chip--accent' => $loop->index === 1]) data-hero-item data-depth="{{ $depths[$loop->index % 3] }}">
                        <span class="kpi-chip__icon"><x-icon :name="$kpi['icon']" :size="18" /></span>
                        <span class="kpi-chip__label">{{ $kpi['label'] }}</span>
                        <span class="kpi-chip__value" data-count="{{ $kpi['value'] }}" data-decimals="{{ $kpi['decimals'] }}" data-prefix="{{ $kpi['prefix'] }}" data-suffix="{{ $kpi['suffix'] }}">{{ $kpi['prefix'] }}{{ number_format((float) $kpi['value'], (int) $kpi['decimals']) }}{{ $kpi['suffix'] }}</span>
                    </div>
                @endforeach
                @if ($p['hero']['kpi_note'])
                    <p class="hero-kpis__note" data-hero-item><x-icon name="check" :size="14" /> {{ $p['hero']['kpi_note'] }}</p>
                @endif
            </div>
        </div>
        <div class="container hero-meta" aria-hidden="true" data-hero-item>
            <span class="hero-meta__scroll"><span class="scroll-cue"><span></span></span>Scroll to explore</span>
            <span class="hero-meta__count">{{ $p['hero']['meta_strong'] }} <span>{{ $p['hero']['meta_text'] }}</span></span>
        </div>
    </section>

    {{-- ============ MARQUEE ============ --}}
    <section class="marquee" aria-label="Platforms, tools and skills">
        @foreach (array_filter([config('portfolio.tools'), $p['marquee']['second_row']]) as $row)
            <div @class(['marquee__row', 'marquee__row--reverse' => $loop->index === 1])>
                <div class="marquee__track">
                    @foreach ([1, 2] as $copy)
                        <ul class="marquee__list" @if ($copy === 2) aria-hidden="true" @endif>
                            @foreach ($row as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    {{-- ============ ABOUT PREVIEW ============ --}}
    <section class="section section--glow">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-5" data-reveal>
                    <div class="portrait-stage" data-tilt data-tilt-max="6">
                        <span class="orbit-ring orbit-ring--a" aria-hidden="true"><i></i></span>
                        <span class="orbit-ring orbit-ring--b" aria-hidden="true"><i></i></span>
                        <figure class="portrait">
                            <img src="{{ asset($profile['photo']) }}" alt="Portrait of {{ $profile['name'] }}" width="400" height="400" loading="lazy" decoding="async">
                        </figure>
                        @foreach (array_slice($p['about']['chips'], 0, 3) as $chip)
                            <span class="float-chip float-chip--{{ $loop->iteration }}" aria-hidden="true"><x-icon :name="$chip['icon']" :size="16" /> {{ $chip['label'] }}</span>
                        @endforeach
                        <div class="portrait__badge"><span>{{ $profile['years'] }}</span> {{ $p['about']['badge_text'] }}</div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <x-section-head :eyebrow="$p['about']['eyebrow']" :title="$p['about']['title']" />
                    @if ($profile['intro'])
                        <p class="prose prose--lg" data-reveal>{{ $profile['intro'][0] }}</p>
                    @endif
                    <ul class="strength-list" data-reveal-stagger>
                        @foreach (array_slice($profile['strengths'], 0, 2) as $strength)
                            <li class="card card--compact">
                                <span class="icon-badge icon-badge--{{ $tones[$loop->index] }}"><x-icon :name="$strength['icon']" /></span>
                                <div><strong>{{ $strength['title'] }}</strong><span>{{ $strength['text'] }}</span></div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('about') }}" class="link-arrow" data-reveal>{{ $p['about']['link_label'] }} <x-icon name="arrow-right" /></a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SERVICES BENTO ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <div class="section-row">
                <x-section-head :eyebrow="$p['services']['eyebrow']" :title="$p['services']['title']" />
                <a href="{{ route('services') }}" class="link-arrow" data-reveal>{{ str_replace(':count', count(config('portfolio.services')), $p['services']['link_label']) }} <x-icon name="arrow-right" /></a>
            </div>
            <div class="bento bento--services" data-reveal-stagger>
                @foreach ($services as $service)
                    <a href="{{ route('services') }}#{{ $service['slug'] }}" @class(['card service-mini', 'service-mini--feature' => $loop->first]) data-tilt>
                        <span class="icon-badge icon-badge--lg icon-badge--{{ $tones[$loop->index] }}"><x-icon :name="$service['icon']" :size="24" /></span>
                        @if ($loop->first)
                            {{-- Illustrative weekly planner — shows the kind of work, not data. --}}
                            <div class="mini-planner" aria-hidden="true">
                                <div class="mini-planner__head">
                                    <span><x-icon name="calendar" :size="14" /> Content calendar</span>
                                    <span class="mini-planner__chip">This week</span>
                                </div>
                                <div class="mini-planner__grid">
                                    <span class="mini-planner__scan"></span>
                                    @foreach (['M' => ['reel'], 'T' => ['post'], 'W' => ['story', 'ad'], 'T ' => ['reel'], 'F' => ['post', 'story'], 'S' => ['ad'], 'S ' => []] as $day => $slots)
                                        <div class="mini-planner__day" style="--d: {{ $loop->index }}">
                                            <span class="mini-planner__label">{{ trim($day) }}</span>
                                            @foreach ($slots as $slot)
                                                <span class="mini-planner__slot mini-planner__slot--{{ $slot }}" style="--s: {{ $loop->index }}">{{ ucfirst($slot) }}</span>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['text'] }}</p>
                        @if ($loop->first)
                            <ul class="check-list check-list--compact">
                                @foreach ($service['benefits'] as $benefit)
                                    <li>{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <span class="card__corner" aria-hidden="true"><x-icon name="arrow-up-right" :size="18" /></span>
                    </a>
                @endforeach
                <a href="{{ route('services') }}" class="card bento-cta" data-tilt>
                    <span class="bento-cta__count">{{ count(config('portfolio.services')) }}</span>
                    <span class="bento-cta__text">{{ $p['services']['cta_text'] }}</span>
                    <span class="btn btn--dark btn--sm">{{ $p['services']['cta_button'] }} <x-icon name="arrow-right" :size="16" /></span>
                </a>
            </div>
        </div>
    </section>

    {{-- ============ PORTFOLIO PREVIEW ============ --}}
    <section class="section">
        <div class="container">
            <div class="section-row">
                <x-section-head :eyebrow="$p['portfolio']['eyebrow']" :title="$p['portfolio']['title']" />
                <a href="{{ route('portfolio') }}" class="link-arrow" data-reveal>{{ $p['portfolio']['link_label'] }} <x-icon name="arrow-right" /></a>
            </div>
            <div class="row g-4" data-reveal-stagger>
                @foreach ($featured as $item)
                    <div class="col-lg-6">
                        <a href="{{ route('portfolio') }}#{{ $item['slug'] }}" class="card work-feature" data-tilt data-tilt-max="5">
                            <div class="browser">
                                <div class="browser__bar" aria-hidden="true"><i></i><i></i><i></i><span>{{ $item['media_type'] === 'video' ? 'Screen recording' : 'Meta Ads Manager' }}</span></div>
                                <div class="work-feature__media">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['media_type'] === 'video' ? 'Preview frame of a screen recording' : 'Meta Ads Manager screenshot' }}: {{ $item['title'] }}" @if ($item['width']) width="{{ $item['width'] }}" height="{{ $item['height'] }}" @endif loading="lazy" decoding="async">
                                </div>
                            </div>
                            <div class="work-feature__body">
                                <span class="tag">{{ $item['client'] }}</span>
                                <h3>{{ $item['title'] }}</h3>
                                <ul class="stat-pills">
                                    @foreach ($item['stats'] as $stat)
                                        <li>{{ $stat }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <span class="card__corner" aria-hidden="true"><x-icon name="arrow-up-right" :size="18" /></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ RESULTS PREVIEW ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <div class="section-row">
                <x-section-head :eyebrow="$p['results']['eyebrow']" :title="$p['results']['title']" />
                <a href="{{ route('results') }}" class="link-arrow" data-reveal>{{ $p['results']['link_label'] }} <x-icon name="arrow-right" /></a>
            </div>
            <div class="counter-grid" data-reveal-stagger>
                @foreach ($results['career'] as $stat)
                    <div class="counter card" data-tilt>
                        <span class="counter__value"><span data-count="{{ $stat['value'] }}" data-suffix="{{ $stat['suffix'] }}">{{ $stat['value'] }}{{ $stat['suffix'] }}</span></span>
                        <span class="counter__label">{{ $stat['label'] }}</span>
                        <span class="counter__bar" aria-hidden="true"></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ INTERNATIONAL PREVIEW ============ --}}
    <section class="section intl-preview section--glow">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-6 order-lg-2">
                    <div class="globe-mini" data-scene="globe" data-lazy data-markets='@json($globeData)' aria-hidden="true">
                        <canvas></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <x-section-head :eyebrow="$p['international']['eyebrow']" :title="$p['international']['title']">{{ $p['international']['lead'] }}</x-section-head>
                    <ul class="market-chips" data-reveal-stagger>
                        @foreach ($markets as $market)
                            <li><span class="code-badge" aria-hidden="true">{{ $market['code'] }}</span> {{ $market['name'] }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('international') }}" class="link-arrow" data-reveal>{{ $p['international']['link_label'] }} <x-icon name="arrow-right" /></a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
