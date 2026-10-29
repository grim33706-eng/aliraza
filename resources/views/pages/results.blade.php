@php
    $p = config('portfolio.pages.results');
    // Vertical bar chart geometry (SVG user units).
    $chartH = 220; $barW = 26; $gap = 14; $padL = 36; $padT = 24;
    $maxLeads = $leads_chart ? max(max($leads_chart), 1) : 1;
    $ctrMax = $ctr_chart ? max(max(array_column($ctr_chart, 'value')), 0.01) : 1;
    $sourceLabels = collect($sources)->pluck('label', 'key');
    $tones = ['accent', 'iris', 'coral', 'iris'];
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'Results'" page="results" :description="$p['seo']['description'] ?: config('portfolio.profile.summary')">
    <x-page-hero :eyebrow="$p['hero']['eyebrow']" index="04" scene="skyline" :watermark="$p['hero']['watermark']" :lead="$p['hero']['lead']">
        <x-slot:title><x-rich :text="$p['hero']['title']" /></x-slot:title>
    </x-page-hero>

    {{-- ============ HEADLINE NUMBERS ============ --}}
    <section class="section section--glow">
        <div class="container">
            <x-section-head :eyebrow="$p['highlights']['eyebrow']" :title="$p['highlights']['title']" />
            <div class="metric-grid" data-reveal-stagger>
                @foreach ($highlights as $metric)
                    <div @class(['metric card', 'metric--feature' => $loop->index < 2]) data-tilt data-tilt-max="5">
                        <span class="metric__top">
                            <span class="metric__label">{{ $metric['label'] }}</span>
                            @if ($metric['source'] && $sourceLabels->has($metric['source']))
                                <a href="#source-{{ \Illuminate\Support\Str::slug($metric['source']) }}" class="metric__source" aria-label="Source: {{ $sourceLabels[$metric['source']] }}">{{ $metric['source'] }}</a>
                            @endif
                        </span>
                        <span class="metric__value">
                            <span data-count="{{ $metric['value'] }}" data-decimals="{{ $metric['decimals'] }}" data-prefix="{{ $metric['prefix'] }}" data-suffix="{{ $metric['suffix'] }}">{{ $metric['prefix'] }}{{ number_format((float) $metric['value'], (int) $metric['decimals']) }}{{ $metric['suffix'] }}</span>
                        </span>
                        @if ($loop->index < 2)<span class="metric__wave" aria-hidden="true"></span>@endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CHARTS ============ --}}
    @if ($leads_chart || $ctr_chart)
        <section class="section section--alt section--grid">
            <div class="container">
                <x-section-head :eyebrow="$p['charts']['eyebrow']" :title="$p['charts']['title']" />
                @if ($leads_chart)
                    <figure class="card chart-card mb-4" data-reveal>
                        <figcaption class="chart-card__head">
                            <div>
                                <p class="eyebrow">{{ $p['charts']['leads_eyebrow'] }}</p>
                                <h3 class="chart-card__title">{{ $p['charts']['leads_title'] }}</h3>
                                <p class="chart-card__sub">{{ str_replace([':count', ':total'], [count($leads_chart), number_format(array_sum($leads_chart))], $p['charts']['leads_sub']) }}</p>
                            </div>
                            <span class="chart-card__total"><strong>{{ number_format(array_sum($leads_chart)) }}</strong> total</span>
                        </figcaption>
                        <svg class="bar-chart" viewBox="0 0 {{ $padL + count($leads_chart) * ($barW + $gap) }} {{ $chartH + $padT + 30 }}" role="img" aria-label="Bar chart: {{ $p['charts']['leads_title'] }}, from {{ min($leads_chart) }} to {{ max($leads_chart) }}">
                            <defs>
                                <linearGradient id="barGrad" x1="0" y1="1" x2="0" y2="0">
                                    <stop offset="0" class="bar-chart__stop-a" />
                                    <stop offset="1" class="bar-chart__stop-b" />
                                </linearGradient>
                            </defs>
                            @foreach ([0, 0.5, 1] as $tick)
                                @php($y = $padT + $chartH - $tick * $chartH)
                                <line x1="{{ $padL - 6 }}" x2="100%" y1="{{ $y }}" y2="{{ $y }}" class="bar-chart__grid" />
                                <text x="{{ $padL - 10 }}" y="{{ $y + 4 }}" class="bar-chart__axis" text-anchor="end">{{ round($maxLeads * $tick) }}</text>
                            @endforeach
                            @foreach ($leads_chart as $i => $value)
                                @php($h = max(2, $value / $maxLeads * $chartH))
                                @php($x = $padL + $i * ($barW + $gap))
                                <g class="bar-chart__bar" data-bar>
                                    <rect x="{{ $x }}" y="{{ $padT + $chartH - $h }}" width="{{ $barW }}" height="{{ $h }}" rx="6" @class(['is-top' => $value == $maxLeads])><title>Campaign {{ $i + 1 }}: {{ $value }}</title></rect>
                                    <text x="{{ $x + $barW / 2 }}" y="{{ $padT + $chartH - $h - 7 }}" text-anchor="middle" class="bar-chart__value">{{ $value }}</text>
                                    <text x="{{ $x + $barW / 2 }}" y="{{ $padT + $chartH + 18 }}" text-anchor="middle" class="bar-chart__axis">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</text>
                                </g>
                            @endforeach
                        </svg>
                        <table class="visually-hidden">
                            <caption>{{ $p['charts']['leads_title'] }}</caption>
                            <tr><th scope="col">Campaign</th><th scope="col">Value</th></tr>
                            @foreach ($leads_chart as $i => $value)
                                <tr><td>{{ $i + 1 }}</td><td>{{ $value }}</td></tr>
                            @endforeach
                        </table>
                    </figure>
                @endif

                @if ($ctr_chart)
                    <figure class="card chart-card chart-card--split" data-reveal>
                        <div class="radial-stage" data-scene="radial" data-lazy data-values='@json($ctr_chart)' aria-hidden="true"><canvas></canvas></div>
                        <figcaption class="chart-card__legend">
                            <p class="eyebrow">{{ $p['charts']['ctr_eyebrow'] }}</p>
                            <h3 class="chart-card__title">{{ $p['charts']['ctr_title'] }}</h3>
                            <p class="chart-card__sub">{{ str_replace(':count', count($ctr_chart), $p['charts']['ctr_sub']) }}</p>
                            <ul class="hbar-chart" data-radial-legend>
                                @foreach ($ctr_chart as $row)
                                    <li tabindex="0" data-radial-index="{{ $loop->index }}">
                                        <span class="hbar-chart__label">{{ $row['label'] }}</span>
                                        <span class="hbar-chart__track"><span class="hbar-chart__fill" data-hbar style="--w: {{ round($row['value'] / $ctrMax * 100, 2) }}%"></span></span>
                                        <span class="hbar-chart__value">{{ number_format((float) $row['value'], 2) }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </figcaption>
                    </figure>
                @endif
            </div>
        </section>
    @endif

    {{-- ============ ACHIEVEMENTS ============ --}}
    <section class="section">
        <div class="container">
            <x-section-head :eyebrow="$p['achievements']['eyebrow']" :title="$p['achievements']['title']" />
            <div class="counter-grid mb-4" data-reveal-stagger>
                @foreach ($career as $stat)
                    <div class="counter card" data-tilt>
                        <span class="counter__value"><span data-count="{{ $stat['value'] }}" data-suffix="{{ $stat['suffix'] }}">{{ $stat['value'] }}{{ $stat['suffix'] }}</span></span>
                        <span class="counter__label">{{ $stat['label'] }}</span>
                        <span class="counter__bar" aria-hidden="true"></span>
                    </div>
                @endforeach
            </div>
            <div class="bento bento--four" data-reveal-stagger>
                @foreach ($achievements as $achievement)
                    <article class="card strength-card" data-tilt>
                        <span class="card__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="icon-badge icon-badge--lg icon-badge--{{ $tones[$loop->index % 4] }}"><x-icon :name="$achievement['icon']" :size="24" /></span>
                        <h3>{{ $achievement['title'] }}</h3>
                        <p>{{ $achievement['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ MILESTONES ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <x-section-head :eyebrow="$p['milestones']['eyebrow']" :title="$p['milestones']['title']">{{ $p['milestones']['lead'] }}</x-section-head>
            <ol class="stairs" data-stairs style="--cols: {{ max(count($milestones), 1) }}">
                @foreach ($milestones as $step)
                    <li class="stairs__step" style="--i: {{ $loop->index }}" data-stair>
                        <span class="stairs__date">{{ \Illuminate\Support\Str::before($step['period'], ' –') }}</span>
                        <strong>{{ $step['role'] }}</strong>
                        <span class="stairs__co">{{ $step['company'] }}</span>
                        <span class="stairs__level" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============ SOURCES ============ --}}
    @if ($sources)
        <section class="section">
            <div class="container">
                <x-section-head :eyebrow="$p['sources']['eyebrow']" :title="$p['sources']['title']" />
                <div class="row g-4">
                    @foreach ($sources as $source)
                        <div class="col-lg-6" id="source-{{ \Illuminate\Support\Str::slug($source['key']) }}" data-reveal>
                            <a href="{{ asset($source['image']) }}" class="card source-card" target="_blank" rel="noopener" data-tilt data-tilt-max="4">
                                <div class="browser">
                                    <div class="browser__bar" aria-hidden="true"><i></i><i></i><i></i><span>Source {{ $source['key'] }}</span></div>
                                    <img src="{{ asset($source['image']) }}" alt="Screenshot — {{ $source['label'] }}" loading="lazy" decoding="async">
                                </div>
                                <span class="source-card__caption"><strong>Source {{ $source['key'] }}</strong> · {{ $source['label'] }} <x-icon name="arrow-up-right" :size="16" /></span>
                            </a>
                        </div>
                    @endforeach
                </div>
                @if ($p['sources']['footnote'])
                    <p class="footnote" data-reveal>{{ $p['sources']['footnote'] }}</p>
                @endif
            </div>
        </section>
    @endif
</x-layouts.app>
