@php
    $p = config('portfolio.pages.portfolio');
    // An item shows media when it has a screenshot/poster; a recording also needs its video file.
    $items = collect($items)->map(function ($item) {
        $type = $item['image'] ? ($item['media_type'] === 'video' && $item['video'] ? 'video' : ($item['media_type'] === 'none' ? 'none' : 'image')) : 'none';

        return $item + ['shown_as' => $type];
    });
    $withMedia = $items->where('shown_as', '!=', 'none')->values();
    $galleryImages = $withMedia->map(fn ($i) => [
        'src' => asset($i['image']),
        'w' => $i['width'] ?: 1600,
        'h' => $i['height'] ?: 900,
        'title' => $i['title'],
    ]);
    $tones = ['accent', 'iris', 'coral'];
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'Portfolio'" page="portfolio" :description="$p['seo']['description'] ?: config('portfolio.profile.summary')">
    <x-page-hero :eyebrow="$p['hero']['eyebrow']" index="03" scene="wave" :watermark="$p['hero']['watermark']" :lead="$p['hero']['lead']">
        <x-slot:title><x-rich :text="$p['hero']['title']" /></x-slot:title>
    </x-page-hero>

    {{-- ============ 3D GALLERY ============ --}}
    @if ($withMedia->isNotEmpty())
        <section class="section gallery-section section--glow" aria-labelledby="gallery-title">
            <div class="container">
                <div class="section-row">
                    <div class="section-head" data-reveal>
                        <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span>{{ $p['gallery']['eyebrow'] }}</p>
                        <h2 class="section-title" id="gallery-title"><x-rich :text="str_replace(':count', $withMedia->count(), $p['gallery']['title'])" /></h2>
                    </div>
                    <p class="gallery-section__hint" data-reveal><x-icon name="arrow-right" :size="16" /> {{ $p['gallery']['hint'] }}</p>
                </div>
            </div>
            <div class="gallery-stage" data-scene="gallery" data-lazy data-images='@json($galleryImages)' aria-hidden="true"><canvas></canvas></div>
        </section>
    @endif

    {{-- ============ FILTERABLE GRID ============ --}}
    <section class="section section--alt section--grid">
        <div class="container">
            <x-section-head :eyebrow="$p['grid']['eyebrow']" :title="$p['grid']['title']" />
            <div class="filter-bar" role="group" aria-label="Filter portfolio" data-reveal>
                @foreach ($filters as $filter)
                    <button type="button" class="filter-btn" data-filter="{{ $filter['key'] }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $filter['label'] }}
                        <span class="filter-btn__count">{{ $filter['key'] === 'all' ? $items->count() : $items->filter(fn ($i) => in_array($filter['key'], $i['filters']))->count() }}</span>
                    </button>
                @endforeach
            </div>
            <p class="visually-hidden" aria-live="polite" data-filter-status></p>

            <div class="work-grid" data-work-grid>
                @foreach ($items as $item)
                    @php($type = $item['shown_as'])
                    <article @class(['work-card card', 'work-card--media' => $type !== 'none', 'work-card--wide' => $loop->first]) id="{{ $item['slug'] }}" data-filters="{{ implode(' ', $item['filters']) }}" data-tilt data-tilt-max="4">
                        @if ($type !== 'none')
                            <div class="browser">
                                <div class="browser__bar" aria-hidden="true"><i></i><i></i><i></i><span>{{ $type === 'video' ? 'Screen recording' : 'Meta Ads Manager' }}</span></div>
                                <button type="button" class="work-card__media" data-lightbox data-type="{{ $type }}" data-src="{{ asset($type === 'video' ? $item['video'] : $item['image']) }}" data-title="{{ $item['title'] }}" @if ($type === 'image' && $item['width']) data-width="{{ $item['width'] }}" data-height="{{ $item['height'] }}" @endif aria-label="Open {{ $type === 'video' ? 'recording' : 'screenshot' }}: {{ $item['title'] }}">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $type === 'video' ? 'Preview frame of a screen recording' : 'Screenshot' }}: {{ $item['title'] }}" @if ($item['width']) width="{{ $item['width'] }}" height="{{ $item['height'] }}" @endif loading="lazy" decoding="async">
                                    @if ($type === 'video')
                                        <span class="play-orb" aria-hidden="true"><x-icon name="play" :size="26" /></span>
                                    @endif
                                    <span class="work-card__open">
                                        <x-icon :name="$type === 'video' ? 'play' : 'expand'" :size="16" />
                                        {{ $type === 'video' ? 'Play recording' : 'View full screenshot' }}
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="work-card__top">
                                <span class="icon-badge icon-badge--lg icon-badge--{{ $tones[$loop->index % 3] }}"><x-icon :name="$item['icon']" :size="24" /></span>
                                <span class="card__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        @endif

                        <div class="work-card__body">
                            <p class="work-card__meta">
                                <span class="tag">{{ $item['client'] }}</span>
                                @if ($item['market'])<span class="work-card__market"><x-icon name="pin" :size="14" /> {{ $item['market'] }}</span>@endif
                            </p>
                            <h2 class="work-card__title">{{ $item['title'] }}</h2>
                            <p>{{ $item['text'] }}</p>
                            @if ($item['stats'])
                                <ul class="stat-pills">
                                    @foreach ($item['stats'] as $stat)
                                        <li>{{ $stat }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if ($item['tags'])
                                <ul class="tag-list">
                                    @foreach ($item['tags'] as $tag)
                                        <li>{{ $tag }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="empty-state" data-work-empty hidden>
                <x-icon name="search" :size="28" />
                <p>Nothing in this category yet. <button type="button" class="link-btn" data-filter="all">Show all work</button></p>
            </div>

            @if ($p['grid']['footnote'])
                <p class="footnote" data-reveal>{{ $p['grid']['footnote'] }}</p>
            @endif
        </div>
    </section>

    {{-- Lightbox for screenshots and recordings --}}
    <dialog class="lightbox" data-lightbox-dialog aria-label="Media preview">
        <div class="lightbox__frame">
            <div class="lightbox__bar">
                <span class="lightbox__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                <span class="lightbox__title" data-lightbox-title></span>
                <button type="button" class="icon-btn" data-lightbox-close aria-label="Close preview"><x-icon name="close" /></button>
            </div>
            <div class="lightbox__media" data-lightbox-media></div>
        </div>
    </dialog>
</x-layouts.app>
