@php
    $contact = config('portfolio.contact');
    // Listed top → bottom; WhatsApp sits closest to the toggle.
    $channels = [
        ['name' => 'Email', 'icon' => 'mail', 'url' => 'mailto:'.$contact['email'], 'brand' => 'email', 'external' => false],
        ...collect(config('portfolio.social'))->map(fn ($s) => ['name' => $s['name'], 'icon' => $s['icon'], 'url' => $s['url'], 'brand' => strtolower($s['name']), 'external' => true])->all(),
        ['name' => 'WhatsApp', 'icon' => 'whatsapp', 'url' => $contact['whatsapp'], 'brand' => 'whatsapp', 'external' => true],
    ];
    $channels = array_values(array_filter($channels, fn ($c) => filled($c['url']) && $c['url'] !== 'mailto:'));
@endphp
{{-- Floating contact menu: one button that fans out every channel. --}}
<div class="social-dock" data-dock>
    <ul class="social-dock__list" id="social-dock-list" aria-label="Contact channels">
        @foreach ($channels as $channel)
            <li style="--i: {{ count($channels) - $loop->index }}">
                <a href="{{ $channel['url'] }}" class="social-dock__item social-dock__item--{{ $channel['brand'] }}" @if ($channel['external']) target="_blank" rel="noopener" @endif>
                    <span class="social-dock__label">{{ $channel['name'] }}</span>
                    <span class="social-dock__icon"><x-icon :name="$channel['icon']" :size="19" /></span>
                </a>
            </li>
        @endforeach
    </ul>
    <button type="button" class="social-dock__toggle" data-dock-toggle aria-expanded="false" aria-controls="social-dock-list" aria-label="Open contact links">
        <x-icon name="chat" :size="24" class="social-dock__open" />
        <x-icon name="close" :size="22" class="social-dock__close" />
    </button>
</div>
