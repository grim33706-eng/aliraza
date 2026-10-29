<x-admin.auth-layout title="Sign in">
    <a href="{{ route('home') }}" class="ad-auth__back"><x-icon name="chevron-left" :size="16" /> Back to website</a>
    <h1>Welcome back</h1>
    <p class="ad-auth__sub">Sign in to manage the website.</p>

    @if ($errors->any())
        <div class="ad-alert ad-alert--error" role="alert"><x-icon name="alert" :size="18" /> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}" class="ad-form" novalidate>
        @csrf
        <div class="ad-field">
            <label for="email">Email</label>
            <div class="ad-input-icon">
                <x-icon name="mail" :size="18" />
                <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="you@example.com">
            </div>
        </div>
        <div class="ad-field">
            <div class="ad-field__row">
                <label for="password">Password</label>
                <a href="{{ route('admin.password.request') }}" class="ad-auth__link">Forgot password?</a>
            </div>
            <div class="ad-input-icon">
                <x-icon name="lock" :size="18" />
                <input type="password" id="password" name="password" autocomplete="current-password" required placeholder="••••••••••">
                <button type="button" class="ad-input-icon__toggle" data-password-toggle aria-label="Show password" aria-pressed="false"><x-icon name="eye" :size="18" class="ad-eye-show" /><x-icon name="eye-off" :size="18" class="ad-eye-hide" /></button>
            </div>
        </div>
        <label class="ad-check">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            <span>Keep me signed in on this device</span>
        </label>
        <button type="submit" class="ad-btn ad-btn--primary ad-btn--block ad-btn--lg">Sign in <x-icon name="arrow-right" :size="18" /></button>
    </form>
</x-admin.auth-layout>
