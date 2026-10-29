@props(['eyebrow', 'lead' => null, 'scene', 'index' => null, 'watermark' => null])
{{-- Inner-page hero: Three.js canvas behind, animated headline in front. --}}
<section {{ $attributes->merge(['class' => 'page-hero']) }} data-hero>
    <div class="hero-scene" data-scene="{{ $scene }}" aria-hidden="true">
        <canvas></canvas>
    </div>
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="hero-grid-lines" aria-hidden="true"></div>
    <span class="hero-watermark" aria-hidden="true" data-watermark>{{ $watermark ?? $eyebrow }}</span>

    <div class="container page-hero__inner">
        <p class="eyebrow" data-hero-item>
            @if ($index)<span class="eyebrow__index">{{ $index }}</span>@endif
            {{ $eyebrow }}
        </p>
        <h1 class="page-hero__title" data-split>{{ $title }}</h1>
        @if ($lead)
            <p class="page-hero__lead" data-hero-item>{{ $lead }}</p>
        @endif
        @isset($actions)
            <div class="hero-actions" data-hero-item>{{ $actions }}</div>
        @endisset
    </div>

    <div class="container hero-meta" aria-hidden="true" data-hero-item>
        <span class="hero-meta__scroll"><span class="scroll-cue"><span></span></span>Scroll to explore</span>
        @if ($index)<span class="hero-meta__count">{{ $index }} <span>/ 06</span></span>@endif
    </div>
</section>
