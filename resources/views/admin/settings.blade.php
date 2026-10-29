@php($user = auth()->user())
<x-admin.layout title="Settings" heading="Settings" subheading="Your admin profile, photo and password.">
    <div class="row g-4">
        {{-- ============ PROFILE ============ --}}
        <div class="col-xl-7">
            <section class="ad-card">
                <header class="ad-card__head">
                    <h2><x-icon name="user" :size="18" /> Profile</h2>
                </header>
                <form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data" class="ad-form" data-content-form novalidate>
                    @csrf
                    @method('PUT')

                    <div class="ad-avatar-field" data-avatar-field>
                        <span class="ad-avatar-field__preview" data-avatar-preview>
                            @if ($user->avatar)
                                <img src="{{ asset($user->avatar) }}" alt="Profile photo">
                            @else
                                <span>{{ $user->initials() }}</span>
                            @endif
                        </span>
                        <div>
                            <span class="ad-label">Profile photo</span>
                            <div class="ad-media__actions">
                                <label class="ad-btn ad-btn--ghost ad-btn--sm ad-media__pick">
                                    <x-icon name="upload" :size="16" /> {{ $user->avatar ? 'Change photo' : 'Upload photo' }}
                                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" data-avatar-input>
                                </label>
                                @if ($user->avatar)
                                    <label class="ad-check ad-check--inline">
                                        <input type="checkbox" name="remove_avatar" value="1" data-avatar-remove>
                                        <span>Remove photo</span>
                                    </label>
                                @endif
                            </div>
                            <p class="ad-help">Square images work best — it's cropped to a circle automatically.</p>
                            @error('avatar', 'profile')<p class="ad-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="ad-fields">
                        <div class="ad-fields__cell is-half">
                            <div @class(['ad-field', 'is-invalid' => $errors->profile->has('name')])>
                                <label class="ad-label" for="name">Name</label>
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                                @error('name', 'profile')<p class="ad-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="ad-fields__cell is-half">
                            <div @class(['ad-field', 'is-invalid' => $errors->profile->has('email')])>
                                <label class="ad-label" for="email">Email (used to sign in)</label>
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                                @error('email', 'profile')<p class="ad-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <footer class="ad-section__foot">
                        <span class="ad-dirty" data-dirty hidden><span class="ad-dirty__dot"></span> Unsaved changes</span>
                        <button type="submit" class="ad-btn ad-btn--primary"><x-icon name="save" :size="16" /> Save profile</button>
                    </footer>
                </form>
            </section>
        </div>

        {{-- ============ PASSWORD ============ --}}
        <div class="col-xl-5">
            <section class="ad-card">
                <header class="ad-card__head">
                    <h2><x-icon name="lock" :size="18" /> Change password</h2>
                </header>
                <form method="POST" action="{{ route('admin.settings.password') }}" class="ad-form" novalidate>
                    @csrf
                    @method('PUT')
                    @foreach ([
                        ['name' => 'current_password', 'label' => 'Current password', 'auto' => 'current-password'],
                        ['name' => 'password', 'label' => 'New password', 'auto' => 'new-password'],
                        ['name' => 'password_confirmation', 'label' => 'Confirm new password', 'auto' => 'new-password'],
                    ] as $input)
                        <div @class(['ad-field', 'is-invalid' => $errors->password->has($input['name'])])>
                            <label class="ad-label" for="{{ $input['name'] }}">{{ $input['label'] }}</label>
                            <div class="ad-input-icon">
                                <x-icon name="lock" :size="18" />
                                <input type="password" id="{{ $input['name'] }}" name="{{ $input['name'] }}" autocomplete="{{ $input['auto'] }}" required @if ($input['name'] === 'password') data-password-strength @endif>
                                <button type="button" class="ad-input-icon__toggle" data-password-toggle aria-label="Show password" aria-pressed="false"><x-icon name="eye" :size="18" class="ad-eye-show" /><x-icon name="eye-off" :size="18" class="ad-eye-hide" /></button>
                            </div>
                            @error($input['name'], 'password')<p class="ad-error">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <ul class="ad-rules" data-password-rules>
                        <li data-rule="length">At least 8 characters</li>
                        <li data-rule="letter">Contains a letter</li>
                        <li data-rule="number">Contains a number</li>
                    </ul>
                    <button type="submit" class="ad-btn ad-btn--primary ad-btn--block"><x-icon name="lock" :size="16" /> Update password</button>
                </form>
            </section>

            <section class="ad-card mt-4">
                <header class="ad-card__head">
                    <h2><x-icon name="logout" :size="18" /> Session</h2>
                </header>
                <p class="ad-muted">Signed in as <strong>{{ $user->email }}</strong>. Admin since {{ $user->created_at->format('j M Y') }}.</p>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="ad-btn ad-btn--ghost ad-btn--danger ad-btn--block"><x-icon name="logout" :size="16" /> Sign out</button>
                </form>
            </section>
        </div>
    </div>
</x-admin.layout>
