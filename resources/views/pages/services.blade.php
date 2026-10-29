@php
    $p = config('portfolio.pages.services');
    $tones = ['accent', 'iris', 'coral'];
    $n = 0;
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'Services'" page="services" :description="$p['seo']['description'] ?: config('portfolio.profile.summary')">
    <x-page-hero :eyebrow="$p['hero']['eyebrow']" index="02" scene="shapes" :watermark="$p['hero']['watermark']" :lead="$p['hero']['lead']">
        <x-slot:title><x-rich :text="$p['hero']['title']" /></x-slot:title>
        <x-slot:actions>
            <a href="#services-list" class="btn btn--primary btn--lg" data-magnetic>{{ $p['hero']['primary_label'] }} <x-icon name="arrow-right" /></a>
            <a href="{{ route('contact') }}" class="btn btn--ghost btn--lg">{{ $p['hero']['secondary_label'] }}</a>
        </x-slot:actions>
    </x-page-hero>

    <div id="services-list">
        @foreach ($groups as $group)
            @php($tone = $tones[$loop->index % 3])
            <section @class(['section service-group', 'section--alt section--grid' => $loop->even, 'section--glow' => $loop->odd])>
                <div class="container">
                    <div class="row g-4 g-lg-5">
                        <div class="col-lg-4">
                            <div class="service-group__intro" data-reveal>
                                <span class="service-group__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span>{{ $group['name'] }}</p>
                                <h2 class="section-title"><x-rich :text="$group['title']" /></h2>
                                @if ($group['text'])
                                    <p class="section-lead">{{ $group['text'] }}</p>
                                @endif
                                <p class="service-group__count"><strong>{{ count($group['services']) }}</strong> {{ $p['cards']['count_label'] }}</p>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="service-grid" data-reveal-stagger>
                                @foreach ($group['services'] as $service)
                                    @php($n++)
                                    <article class="card service-card" id="{{ $service['slug'] }}" data-tilt>
                                        <span class="card__index" aria-hidden="true">{{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="icon-badge icon-badge--lg icon-badge--{{ $tone }}"><x-icon :name="$service['icon']" :size="24" /></span>
                                        <h3>{{ $service['title'] }}</h3>
                                        <p>{{ $service['text'] }}</p>
                                        <ul class="check-list check-list--compact">
                                            @foreach ($service['benefits'] as $benefit)
                                                <li>{{ $benefit }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="{{ route('contact', ['subject' => $service['title']]) }}" class="card__cta">
                                            {{ $p['cards']['cta_label'] }} <span class="card__cta-icon"><x-icon name="arrow-right" :size="16" /></span>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    {{-- ============ PROCESS ============ --}}
    <section class="section process-section">
        <div class="container">
            <x-section-head :eyebrow="$p['process']['eyebrow']" :title="$p['process']['title']" center>{{ $p['process']['lead'] }}</x-section-head>
            <div class="process" data-process>
                <span class="process__line" aria-hidden="true"><span data-process-progress></span></span>
                <ol class="process__steps">
                    @foreach (config('portfolio.process') as $step)
                        <li class="card process__step" data-process-step data-tilt>
                            <span class="process__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ============ TOOLKIT (3D orbit) ============ --}}
    <section class="section section--alt section--grid toolkit">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-5">
                    <x-section-head :eyebrow="$p['toolkit']['eyebrow']" :title="$p['toolkit']['title']">{{ $p['toolkit']['lead'] }}</x-section-head>
                    <ul class="tool-cloud" data-reveal-stagger>
                        @foreach (config('portfolio.tools') as $tool)
                            <li>{{ $tool }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-7">
                    <div class="scene-panel scene-panel--wide" data-reveal>
                        <div class="scene-host" data-scene="orbit" data-lazy data-labels='@json(config('portfolio.tools'))' aria-hidden="true"><canvas></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
