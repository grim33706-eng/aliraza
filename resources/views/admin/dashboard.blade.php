<x-admin.layout title="Dashboard" :heading="'Welcome back, '.\Illuminate\Support\Str::before(auth()->user()->name, ' ')" subheading="Here's what's happening on your website.">
    <x-slot:actions>
        <a href="{{ route('admin.pages.edit', 'home') }}" class="ad-btn ad-btn--primary"><x-icon name="pencil" :size="16" /> Edit the Home page</a>
    </x-slot:actions>

    {{-- ============ STATS ============ --}}
    <div class="ad-stats">
        <a href="{{ route('admin.messages.index') }}" class="ad-stat">
            <span class="ad-stat__icon ad-tone--accent"><x-icon name="inbox" :size="20" /></span>
            <span class="ad-stat__value">{{ $stats['messages'] }}</span>
            <span class="ad-stat__label">Messages received</span>
        </a>
        <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}" class="ad-stat">
            <span class="ad-stat__icon ad-tone--coral"><x-icon name="mail" :size="20" /></span>
            <span class="ad-stat__value">{{ $stats['unread'] }}</span>
            <span class="ad-stat__label">Unread</span>
        </a>
        <div class="ad-stat">
            <span class="ad-stat__icon ad-tone--iris"><x-icon name="trending" :size="20" /></span>
            <span class="ad-stat__value">{{ $stats['thisWeek'] }}</span>
            <span class="ad-stat__label">New this week</span>
        </div>
        <div class="ad-stat">
            <span class="ad-stat__icon ad-tone--accent"><x-icon name="pencil" :size="20" /></span>
            <span class="ad-stat__value">{{ $stats['customized'] }}<small>/{{ $stats['sections'] }}</small></span>
            <span class="ad-stat__label">Sections customised</span>
        </div>
    </div>

    <div class="row g-4">
        {{-- ============ RECENT MESSAGES ============ --}}
        <div class="col-xl-7">
            <section class="ad-card h-100">
                <header class="ad-card__head">
                    <h2><x-icon name="inbox" :size="18" /> Latest messages</h2>
                    <a href="{{ route('admin.messages.index') }}" class="ad-link">View all <x-icon name="arrow-right" :size="14" /></a>
                </header>
                @forelse ($recentMessages as $message)
                    <a href="{{ route('admin.messages.show', $message) }}" @class(['ad-msg-row', 'is-unread' => ! $message->isRead()])>
                        <span class="ad-avatar ad-avatar--sm ad-avatar--soft">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                        <span class="ad-msg-row__body">
                            <strong>{{ $message->name }}</strong>
                            <span>{{ $message->subject }}</span>
                        </span>
                        <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans(short: true) }}</time>
                    </a>
                @empty
                    <div class="ad-empty">
                        <x-icon name="inbox" :size="28" />
                        <p>No messages yet. When someone uses the contact form, it lands here.</p>
                    </div>
                @endforelse
            </section>
        </div>

        {{-- ============ RECENT EDITS ============ --}}
        <div class="col-xl-5">
            <section class="ad-card h-100">
                <header class="ad-card__head">
                    <h2><x-icon name="clock" :size="18" /> Recent edits</h2>
                </header>
                @forelse ($recentEdits as $edit)
                    <a href="{{ $edit['page'] ? route('admin.pages.edit', $edit['page']).'#section-'.$edit['section'] : '#' }}" class="ad-edit-row">
                        <span class="ad-edit-row__dot"></span>
                        <span class="ad-edit-row__body">
                            <strong>{{ $edit['sectionTitle'] }}</strong>
                            <span>{{ $edit['pageLabel'] ?? 'Content' }}</span>
                        </span>
                        <time>{{ $edit['row']->updated_at->diffForHumans(short: true) }}</time>
                    </a>
                @empty
                    <div class="ad-empty">
                        <x-icon name="pencil" :size="28" />
                        <p>Nothing edited yet — the site is showing its original content.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>

    {{-- ============ PAGES ============ --}}
    <h2 class="ad-section-title">Website pages</h2>
    <div class="ad-page-grid">
        @foreach ($pages as $page)
            <a href="{{ route('admin.pages.edit', $page['slug']) }}" class="ad-page-tile">
                <span class="ad-page-tile__icon"><x-icon :name="$page['icon']" :size="22" /></span>
                <strong>{{ $page['label'] }}</strong>
                <span class="ad-page-tile__desc">{{ $page['description'] }}</span>
                <span class="ad-page-tile__meta">
                    <span>{{ count($page['sections']) }} sections</span>
                    @if ($page['edited'])<span class="ad-pill ad-pill--accent">{{ $page['edited'] }} edited</span>@else<span class="ad-pill">Original</span>@endif
                </span>
                <span class="ad-page-tile__go" aria-hidden="true"><x-icon name="arrow-right" :size="16" /></span>
            </a>
        @endforeach
    </div>
</x-admin.layout>
