@props(['user', 'size' => 'md'])
<span {{ $attributes->class(['ad-avatar', "ad-avatar--{$size}"]) }}>
    @if ($user->avatar)
        <img src="{{ asset($user->avatar) }}" alt="" width="80" height="80">
    @else
        {{ $user->initials() }}
    @endif
</span>
