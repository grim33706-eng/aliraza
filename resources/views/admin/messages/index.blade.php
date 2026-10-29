<x-admin.layout title="Messages" heading="Messages" subheading="Everything sent through the contact form on the website.">
    <div class="ad-card ad-card--flush">
        <div class="ad-toolbar">
            <div class="ad-tabs" role="tablist">
                <a href="{{ route('admin.messages.index', array_filter(['q' => $search])) }}" @class(['ad-tab', 'is-active' => $filter === 'all'])>All</a>
                <a href="{{ route('admin.messages.index', array_filter(['filter' => 'unread', 'q' => $search])) }}" @class(['ad-tab', 'is-active' => $filter === 'unread'])>
                    Unread @if ($unreadMessages)<span class="ad-badge">{{ $unreadMessages }}</span>@endif
                </a>
            </div>
            <form method="GET" class="ad-search" role="search">
                @if ($filter === 'unread')<input type="hidden" name="filter" value="unread">@endif
                <x-icon name="search" :size="16" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name, email or text…" aria-label="Search messages">
            </form>
        </div>

        @forelse ($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}" @class(['ad-inbox-row', 'is-unread' => ! $message->isRead()])>
                <span class="ad-inbox-row__dot" aria-label="{{ $message->isRead() ? 'Read' : 'Unread' }}"></span>
                <span class="ad-avatar ad-avatar--sm ad-avatar--soft">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                <span class="ad-inbox-row__from">
                    <strong>{{ $message->name }}</strong>
                    <span>{{ $message->email }}</span>
                </span>
                <span class="ad-inbox-row__text">
                    <strong>{{ $message->subject }}</strong>
                    <span>{{ \Illuminate\Support\Str::limit($message->message, 110) }}</span>
                </span>
                <time datetime="{{ $message->created_at->toIso8601String() }}" title="{{ $message->created_at->format('j M Y, H:i') }}">{{ $message->created_at->diffForHumans(short: true) }}</time>
            </a>
        @empty
            <div class="ad-empty ad-empty--lg">
                <x-icon name="inbox" :size="34" />
                <p>{{ $search ? 'No messages match “'.$search.'”.' : ($filter === 'unread' ? 'You\'re all caught up — no unread messages.' : 'No messages yet. Enquiries from the contact form will appear here.') }}</p>
            </div>
        @endforelse
    </div>

    @if ($messages->hasPages())
        <nav class="ad-pagination" aria-label="Pages">
            @if ($messages->onFirstPage())
                <span class="ad-btn ad-btn--ghost ad-btn--sm is-disabled"><x-icon name="chevron-left" :size="16" /> Newer</span>
            @else
                <a href="{{ $messages->previousPageUrl() }}" class="ad-btn ad-btn--ghost ad-btn--sm"><x-icon name="chevron-left" :size="16" /> Newer</a>
            @endif
            <span>Page {{ $messages->currentPage() }} of {{ $messages->lastPage() }}</span>
            @if ($messages->hasMorePages())
                <a href="{{ $messages->nextPageUrl() }}" class="ad-btn ad-btn--ghost ad-btn--sm">Older <x-icon name="chevron-right" :size="16" /></a>
            @else
                <span class="ad-btn ad-btn--ghost ad-btn--sm is-disabled">Older <x-icon name="chevron-right" :size="16" /></span>
            @endif
        </nav>
    @endif
</x-admin.layout>
