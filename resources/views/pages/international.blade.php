@php
    $p = config('portfolio.pages.international');
    $markerData = $markets->map(fn ($m) => ['name' => $m['name'], 'lat' => $m['lat'], 'lng' => $m['lng']])->values();
    $globeData = ['home' => $home, 'markets' => $markerData];
    $tones = ['accent', 'iris', 'coral', 'iris'];
    $marketIndex = 0; // running index into $markets across all regions
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'International Experience'" page="international" :description="$p['seo']['description'] ?: config('portfolio.profile.summary')">
    {{-- ============ HERO: 3D world map ============ --}}
    <section class="page-hero intl-hero" data-hero>
        <div class="hero-scene" data-scene="worldmap" data-markets='@json($globeData)' aria-hidden="true"><canvas></canvas></div>
        <div class="hero-glow" aria-hidden="true"></div>
        <span class="hero-watermark" aria-hidden="true" data-watermark>{{ $p['hero']['watermark'] }}</span>

        <div class="container page-hero__inner">
            <p class="eyebrow" data-hero-item><span class="eyebrow__index">05</span>{{ $p['hero']['eyebrow'] }}</p>
            <h1 class="page-hero__title" data-split><x-rich :text="$p['hero']['title']" /></h1>
            <p class="page-hero__lead" data-hero-item>{{ $p['hero']['lead'] }}</p>
            <dl class="intl-stats" data-hero-item>
                <div class="card card--compact"><dt>Countries</dt><dd data-count="{{ $markets->count() }}">{{ $markets->count() }}</dd></div>
                <div class="card card--compact"><dt>Regions</dt><dd data-count="{{ count($regions) }}">{{ count($regions) }}</dd></div>
                @if ($remote)
                    <div class="card card--compact"><dt>Remote roles</dt><dd data-count="{{ count($remote) }}">{{ count($remote) }}</dd></div>
                @endif
            </dl>
        </div>
        <div class="container hero-meta" aria-hidden="true" data-hero-item>
            <span class="hero-meta__scroll"><span class="scroll-cue"><span></span></span>{{ $p['hero']['scroll_hint'] }}</span>
            <span class="hero-meta__count">05 <span>/ 06</span></span>
        </div>
    </section>

    {{-- ============ SCROLL STORY: sticky globe + regions ============ --}}
    <section class="section globe-story section--glow" data-globe-story>
        <div class="container">
            <x-section-head :eyebrow="$p['story']['eyebrow']" :title="$p['story']['title']">{{ $p['story']['lead'] }}</x-section-head>
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="globe-story__sticky">
                        <div class="globe-stage" data-scene="globe" data-interactive data-labels data-markets='@json($globeData)' role="img" aria-label="Interactive globe with arcs from {{ $home['name'] }} to {{ $markets->pluck('name')->join(', ', ' and ') }}">
                            <canvas></canvas>
                            <p class="globe-stage__hint" aria-hidden="true">Drag to rotate</p>
                        </div>
                        <ol class="globe-story__dots" aria-hidden="true">
                            @foreach ($regions as $region)
                                <li data-story-dot>{{ $region['name'] }}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
                <div class="col-lg-6">
                    @foreach ($regions as $region)
                        @php($indices = range($marketIndex, $marketIndex + count($region['countries']) - 1))
                        @php($marketIndex += count($region['countries']))
                        <article class="region-chapter" data-region='@json($region['countries'] ? $indices : [])'>
                            <header class="region-chapter__head">
                                <span class="region-chapter__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <h3 class="region-chapter__title">{{ $region['name'] }}</h3>
                                    @if ($region['copy'])<p>{{ $region['copy'] }}</p>@endif
                                </div>
                            </header>
                            <ul class="country-list">
                                @foreach ($region['countries'] as $country)
                                    <li>
                                        <button type="button" class="card country-card" data-market="{{ $indices[$loop->index] }}">
                                            <span class="code-badge code-badge--lg" aria-hidden="true">{{ $country['code'] }}</span>
                                            <span class="country-card__body">
                                                <strong>{{ $country['name'] }}</strong>
                                                <span class="country-card__via">{{ $country['via'] }}</span>
                                                <span class="country-card__work">{{ $country['work'] }}</span>
                                            </span>
                                            <span class="country-card__locate" aria-hidden="true"><x-icon name="target" :size="16" /></span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ROUTES BOARD ============ --}}
    @if ($routes->isNotEmpty())
        <section class="section section--alt section--grid">
            <div class="container">
                <x-section-head :eyebrow="$p['routes']['eyebrow']" :title="$p['routes']['title']">{{ $p['routes']['lead'] }}</x-section-head>
                <div class="route-board card" data-reveal>
                    <div class="route-board__head" aria-hidden="true">
                        <span>From</span><span>To</span><span>Market</span><span class="text-end">Distance</span>
                    </div>
                    <ol class="route-board__list" data-reveal-stagger>
                        @foreach ($routes as $route)
                            <li class="route-row">
                                <span class="route-row__from">{{ $p['routes']['from_code'] }}</span>
                                <span class="route-row__to"><span class="route-row__line" aria-hidden="true"><i></i></span>{{ $route['city'] }}</span>
                                <span class="route-row__market"><span class="code-badge" aria-hidden="true">{{ $route['code'] }}</span> {{ $route['name'] }}</span>
                                <span class="route-row__km"><span data-count="{{ $route['km'] }}">{{ number_format($route['km']) }}</span> km</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ APPROACH ============ --}}
    <section class="section">
        <div class="container">
            <x-section-head :eyebrow="$p['approach']['eyebrow']" :title="$p['approach']['title']" center />
            <div class="bento bento--four" data-reveal-stagger>
                @foreach (config('portfolio.international.approach') as $point)
                    <article class="card strength-card" data-tilt>
                        <span class="card__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="icon-badge icon-badge--lg icon-badge--{{ $tones[$loop->index % 4] }}"><x-icon :name="$point['icon']" :size="24" /></span>
                        <h3>{{ $point['title'] }}</h3>
                        <p>{{ $point['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
