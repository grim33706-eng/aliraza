<x-admin.auth-layout title="Choose a new password">
    <a href="{{ route('admin.login') }}" class="ad-auth__back"><x-icon name="chevron-left" :size="16" /> Back to sign in</a>
    <span class="ad-auth__badge"><x-icon name="lock" :size="26" /></span>
    <h1>Choose a new password</h1>
    <p class="ad-auth__sub">Pick something you haven't used before. You'll sign in with it straight after.</p>

    @if ($errors->any())
        <div class="ad-alert ad-alert--error" role="alert"><x-icon name="alert" :size="18" /> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}" class="ad-form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="ad-field">
            <label for="email">Email</label>
            <div class="ad-input-icon">
                <x-icon name="mail" :size="18" />
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" autocomplete="username" required>
            </div>
        </div>
        @foreach ([['password', 'New password'], ['password_confirmation', 'Confirm new password']] as [$name, $label])
            <div class="ad-field">
                <label for="{{ $name }}">{{ $label }}</label>
                <div class="ad-input-icon">
                    <x-icon name="lock" :size="18" />
                    <input type="password" id="{{ $name }}" name="{{ $name }}" autocomplete="new-password" required @if ($name === 'password') data-password-strength autofocus @endif>
                    <button type="button" class="ad-input-icon__toggle" data-password-toggle aria-label="Show password" aria-pressed="false"><x-icon name="eye" :size="18" class="ad-eye-show" /><x-icon name="eye-off" :size="18" class="ad-eye-hide" /></button>
                </div>
            </div>
        @endforeach
        <ul class="ad-rules" data-password-rules>
            <li data-rule="length">At least 8 characters</li>
            <li data-rule="letter">Contains a letter</li>
            <li data-rule="number">Contains a number</li>
        </ul>
        <button type="submit" class="ad-btn ad-btn--primary ad-btn--block ad-btn--lg">Save new password <x-icon name="check" :size="18" /></button>
    </form>
</x-admin.auth-layout>
