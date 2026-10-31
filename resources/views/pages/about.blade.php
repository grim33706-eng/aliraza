@php
    $profile = config('portfolio.profile');
    $education = config('portfolio.education');
    $p = config('portfolio.pages.about');
    $tones = ['accent', 'iris', 'coral', 'iris'];
    $linkedin = collect(config('portfolio.social'))->firstWhere('icon', 'linkedin');
    $skillLabels = collect(config('portfolio.skills'))->values()
        // Short labels for the 3D sphere: "Meta Ads & Meta Pixel Setup" → "Meta Ads".
        ->flatMap(fn ($group, $index) => collect($group['skills'])->map(fn ($s) => ['text' => preg_split('/ & |: | \(/', $s)[0], 'group' => $index]))
        ->values();

    // Education years, e.g. "2021 – 2025" → 2021 and 2025 (track hidden if they can't be read).
    preg_match_all('/\d{4}/', $education['period'], $years);
    [$startYear, $endYear] = count($years[0]) >= 2 ? array_map('intval', [$years[0][0], end($years[0])]) : [null, null];
    $hasTrack = $startYear && $endYear && $endYear > $startYear && $endYear - $startYear <= 10;
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'About'" page="about" :description="$p['seo']['description'] ?: $profile['summary']">
    <x-page-hero :eyebrow="$p['hero']['eyebrow']" index="01" scene="constellation" :watermark="$p['hero']['watermark']" :lead="$p['hero']['lead']">
        <x-slot:title><x-rich :text="$p['hero']['title']" /></x-slot:title>
    </x-page-hero>

    {{-- ============ PROFILE ============ --}}
    <section class="section section--glow">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-5">
                    <div class="profile-card" data-reveal>
                        <div class="portrait-stage" data-tilt data-tilt-max="6">
                            <span class="orbit-ring orbit-ring--a" aria-hidden="true"><i></i></span>
                            <span class="orbit-ring orbit-ring--b" aria-hidden="true"><i></i></span>
                            <figure class="portrait portrait--tall">
                                <img src="{{ asset($profile['photo']) }}" alt="Portrait of {{ $profile['name'] }}, Meta Ads &amp; Google Ads expert and performance marketer" width="400" height="400" decoding="async">
                            </figure>
                            <div class="portrait__badge portrait__badge--name"><strong>{{ $profile['name'] }}</strong><span>{{ $profile['short_title'] }}</span></div>
                        </div>
                        <dl class="fact-grid">
                            @foreach ($p['intro']['facts'] as $fact)
                                <div class="card card--compact"><dt><x-icon :name="$fact['icon']" :size="16" /> {{ $fact['label'] }}</dt><dd>{{ $fact['value'] }}</dd></div>
                            @endforeach
                        </dl>
                    </div>
                </div>
                <div class="col-lg-7">
                    <x-section-head :eyebrow="$p['intro']['eyebrow']" :title="$p['intro']['title']" />
                    <div class="prose prose--lg prose--lead-first" data-reveal-stagger>
                        @foreach ($profile['intro'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                    <div class="hero-actions mt-4" data-reveal>
                        <a href="{{ route('contact') }}" class="btn btn--primary" data-magnetic>{{ $p['intro']['primary_label'] }} <x-icon name="arrow-right" /></a>
                        @if ($linkedin)
                            <a href="{{ $linkedin['url'] }}" class="btn btn--ghost" target="_blank" rel="noopener"><x-icon name="linkedin" /> {{ $p['intro']['secondary_label'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STRENGTHS ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <x-section-head :eyebrow="$p['strengths']['eyebrow']" :title="$p['strengths']['title']" center />
            <div class="bento bento--four" data-reveal-stagger>
                @foreach ($profile['strengths'] as $strength)
                    <article class="card strength-card" data-tilt>
                        <span class="card__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="icon-badge icon-badge--lg icon-badge--{{ $tones[$loop->index % 4] }}"><x-icon :name="$strength['icon']" :size="24" /></span>
                        <h3>{{ $strength['title'] }}</h3>
                        <p>{{ $strength['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CAREER TIMELINE ============ --}}
    <section class="section" id="experience">
        <div class="container">
            <x-section-head :eyebrow="$p['experience']['eyebrow']" :title="$p['experience']['title']" center>{{ $p['experience']['lead'] }}</x-section-head>

            <div class="timeline" data-timeline>
                <span class="timeline__rail" aria-hidden="true"><span class="timeline__progress" data-timeline-progress></span></span>
                <ol class="timeline__list">
                    @foreach (config('portfolio.experience') as $job)
                        <li class="timeline__item" data-timeline-item>
                            <span class="timeline__dot" aria-hidden="true"></span>
                            <article class="card timeline__card" data-tilt data-tilt-max="4">
                                <header class="timeline__head">
                                    <span class="monogram" aria-hidden="true">{{ collect(preg_split('/[\s.]+/', $job['company']))->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('') }}</span>
                                    <div>
                                        <p class="timeline__period">
                                            {{ $job['period'] }}
                                            @if ($job['current'])<span class="badge-live"><span class="pulse" aria-hidden="true"></span>Current</span>@endif
                                        </p>
                                        <h3>{{ $job['role'] }}</h3>
                                        <p class="timeline__company">{{ $job['company'] }} <span>· {{ $job['mode'] }}</span></p>
                                    </div>
                                </header>
                                <ul class="check-list">
                                    @foreach ($job['points'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- ============ SKILLS ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-6">
                    <x-section-head :eyebrow="$p['skills']['eyebrow']" :title="$p['skills']['title']">{{ $p['skills']['lead'] }}</x-section-head>
                    <div class="skill-groups" data-reveal-stagger>
                        @foreach (config('portfolio.skills') as $group)
                            <div class="card skill-group skill-group--{{ $tones[$loop->index % 3] }}">
                                <h3><span class="skill-group__dot" aria-hidden="true"></span>{{ $group['name'] }} <span class="skill-group__count">{{ count($group['skills']) }}</span></h3>
                                <ul class="skill-chips">
                                    @foreach ($group['skills'] as $skill)
                                        <li>{{ $skill }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="scene-panel scene-panel--square" data-reveal>
                        <div class="scene-host" data-scene="skills" data-lazy data-labels='@json($skillLabels)' aria-hidden="true"><canvas></canvas></div>
                        <p class="scene-panel__hint" aria-hidden="true"><x-icon name="sparkles" :size="14" /> {{ $p['skills']['hint'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ EDUCATION ============ --}}
    <section class="section">
        <div class="container">
            <x-section-head :eyebrow="$p['education']['eyebrow']" :title="$p['education']['title']" />
            <article class="card edu-card" data-reveal data-tilt data-tilt-max="3">
                <div class="edu-card__main">
                    <header class="edu-card__head">
                        <span class="icon-badge icon-badge--xl icon-badge--accent"><x-icon name="graduation" :size="32" /></span>
                        <div>
                            <p class="edu-card__label">{{ $p['education']['label'] }}</p>
                            <h3>{{ $education['degree'] }}</h3>
                        </div>
                    </header>

                    <dl class="edu-facts">
                        <div><dt><x-icon name="home" :size="15" /> Institution</dt><dd>{{ $education['institution'] }}</dd></div>
                        <div><dt><x-icon name="pin" :size="15" /> Location</dt><dd>{{ $education['location'] }}</dd></div>
                        <div><dt><x-icon name="clock" :size="15" /> Duration</dt><dd>@if ($hasTrack){{ $endYear - $startYear }} years · @endif{{ $education['period'] }}</dd></div>
                    </dl>

                    @if ($hasTrack)
                        <div class="edu-track" data-edu-track aria-hidden="true">
                            <div class="edu-track__line"><span class="edu-track__fill" data-edu-fill></span></div>
                            <ol class="edu-track__years">
                                @foreach (range($startYear, $endYear) as $year)
                                    <li data-edu-year>
                                        <span class="edu-track__dot"></span>
                                        <span class="edu-track__year">{{ $year }}</span>
                                        @if ($loop->first)<span class="edu-track__note">Started</span>@elseif ($loop->last)<span class="edu-track__note">Graduated</span>@endif
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>

                {{-- Rotating degree seal --}}
                <div class="edu-seal" aria-hidden="true">
                    <svg class="edu-seal__ring" viewBox="0 0 200 200">
                        <defs><path id="eduSealPath" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0" /></defs>
                        <text><textPath href="#eduSealPath" textLength="500" lengthAdjust="spacing">{{ $p['education']['seal_text'] }} •@if ($hasTrack) {{ $startYear }}–{{ $endYear }} •@endif</textPath></text>
                    </svg>
                    <span class="edu-seal__core"><small>{{ $p['education']['seal_small'] }}</small>{{ $p['education']['seal_big'] }}</span>
                </div>
            </article>
        </div>
    </section>
</x-layouts.app>
