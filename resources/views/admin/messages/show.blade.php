<x-admin.layout title="Message">
    <div class="ad-message-nav">
        <a href="{{ route('admin.messages.index') }}" class="ad-link"><x-icon name="chevron-left" :size="16" /> All messages</a>
        <div>
            @if ($previous)<a href="{{ route('admin.messages.show', $previous) }}" class="ad-icon-btn" aria-label="Newer message" title="Newer"><x-icon name="chevron-up" :size="18" /></a>@endif
            @if ($next)<a href="{{ route('admin.messages.show', $next) }}" class="ad-icon-btn" aria-label="Older message" title="Older"><x-icon name="chevron-down" :size="18" /></a>@endif
        </div>
    </div>

    <article class="ad-card ad-message">
        <header class="ad-message__head">
            <span class="ad-avatar ad-avatar--lg ad-avatar--soft">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
            <div class="ad-message__who">
                <h1>{{ $message->subject }}</h1>
                <p><strong>{{ $message->name }}</strong> · <a href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
            </div>
            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('j M Y, H:i') }}<small>{{ $message->created_at->diffForHumans() }}</small></time>
        </header>

        <div class="ad-message__body">{{ $message->message }}</div>

        <footer class="ad-message__actions">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="ad-btn ad-btn--primary"><x-icon name="reply" :size="16" /> Reply by email</a>
            <form method="POST" action="{{ route('admin.messages.toggle', $message) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="ad-btn ad-btn--ghost"><x-icon name="mail-open" :size="16" /> Mark as {{ $message->isRead() ? 'unread' : 'read' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm-title="Delete this message?" data-confirm-label="Delete message" data-confirm="Delete this message from {{ $message->name }}? This can't be undone.">
                @csrf
                @method('DELETE')
                <button type="submit" class="ad-btn ad-btn--ghost ad-btn--danger"><x-icon name="trash" :size="16" /> Delete</button>
            </form>
        </footer>
    </article>
</x-admin.layout>
