<x-admin.auth-layout title="Reset password">
    <a href="{{ route('admin.login') }}" class="ad-auth__back"><x-icon name="chevron-left" :size="16" /> Back to sign in</a>

    @if (session('reset_sent'))
        <div class="ad-auth__done">
            <span class="ad-auth__badge"><x-icon name="mail" :size="26" /></span>
            <h1>Check your inbox</h1>
            <p class="ad-auth__sub">If <strong>{{ session('reset_sent') }}</strong> belongs to the admin account, a reset link is on its way. It works once and expires in {{ config('auth.passwords.users.expire') }} minutes.</p>
            <ul class="ad-auth__steps">
                <li>Open the email titled <em>“Reset your admin password”</em>.</li>
                <li>Not there after a minute? Check your Spam or Promotions folder.</li>
            </ul>
            @if ($mailIsLogged)
                <div class="ad-alert ad-alert--warning"><x-icon name="alert" :size="18" /> <span>Email sending isn't set up yet, so the link was written to <code>storage/logs/laravel.log</code> instead. See the README to connect Gmail (free).</span></div>
            @endif
            <a href="{{ route('admin.password.request') }}" class="ad-btn ad-btn--ghost ad-btn--block">Send another link</a>
        </div>
    @else
        <span class="ad-auth__badge"><x-icon name="lock" :size="26" /></span>
        <h1>Forgot your password?</h1>
        <p class="ad-auth__sub">Enter the admin email and we'll send you a secure link to choose a new one.</p>

        @if ($errors->any())
            <div class="ad-alert ad-alert--error" role="alert"><x-icon name="alert" :size="18" /> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.email') }}" class="ad-form" novalidate>
            @csrf
            <div class="ad-field">
                <label for="email">Email</label>
                <div class="ad-input-icon">
                    <x-icon name="mail" :size="18" />
                    <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="you@example.com">
                </div>
            </div>
            <button type="submit" class="ad-btn ad-btn--primary ad-btn--block ad-btn--lg">Email me a reset link <x-icon name="send" :size="18" /></button>
        </form>
    @endif
</x-admin.auth-layout>
