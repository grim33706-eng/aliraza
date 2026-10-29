@php
    $contact = config('portfolio.contact');
    $p = config('portfolio.pages.contact');
@endphp
<x-layouts.app :title="$p['seo']['title'] ?: 'Contact'" page="contact" :description="$p['seo']['description'] ?: config('portfolio.profile.summary')">
    <x-page-hero :eyebrow="$p['hero']['eyebrow']" index="06" scene="ripple" :watermark="$p['hero']['watermark']" :lead="$p['hero']['lead']">
        <x-slot:title><x-rich :text="$p['hero']['title']" /></x-slot:title>
    </x-page-hero>

    <section class="section contact-section section--glow">
        <div class="container">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-5">
                    <div class="scene-panel scene-panel--plane" data-reveal>
                        <div class="scene-host" data-scene="plane" data-lazy aria-hidden="true"><canvas></canvas></div>
                        <div class="scene-panel__caption">
                            <p class="eyebrow"><span class="pulse" aria-hidden="true"></span>{{ $p['plane']['eyebrow'] }}</p>
                            <p>{{ $p['plane']['text'] }}</p>
                        </div>
                    </div>

                    <ul class="contact-list" data-reveal-stagger>
                        <li>
                            <a href="mailto:{{ $contact['email'] }}" class="card contact-item" data-tilt data-tilt-max="4">
                                <span class="icon-badge icon-badge--accent"><x-icon name="mail" /></span>
                                <span><span class="contact-item__label">Email</span><span class="contact-item__value">{{ $contact['email'] }}</span></span>
                                <span class="card__corner" aria-hidden="true"><x-icon name="arrow-up-right" :size="16" /></span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:{{ $contact['phone_e164'] }}" class="card contact-item" data-tilt data-tilt-max="4">
                                <span class="icon-badge icon-badge--iris"><x-icon name="phone" /></span>
                                <span><span class="contact-item__label">Phone</span><span class="contact-item__value">{{ $contact['phone_display'] }}</span></span>
                                <span class="card__corner" aria-hidden="true"><x-icon name="arrow-up-right" :size="16" /></span>
                            </a>
                        </li>
                        @if ($contact['whatsapp'])
                            <li>
                                <a href="{{ $contact['whatsapp'] }}" class="card contact-item" target="_blank" rel="noopener" data-tilt data-tilt-max="4">
                                    <span class="icon-badge icon-badge--accent"><x-icon name="whatsapp" /></span>
                                    <span><span class="contact-item__label">WhatsApp</span><span class="contact-item__value">{{ $p['labels']['whatsapp_text'] }}</span></span>
                                    <span class="card__corner" aria-hidden="true"><x-icon name="arrow-up-right" :size="16" /></span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <div class="card contact-item">
                                <span class="icon-badge icon-badge--coral"><x-icon name="pin" /></span>
                                <span><span class="contact-item__label">Based in</span><span class="contact-item__value">{{ config('portfolio.profile.location') }}</span></span>
                            </div>
                        </li>
                    </ul>

                    @if (config('portfolio.social'))
                        <h3 class="contact-social-title" data-reveal>{{ $p['labels']['social_title'] }}</h3>
                        <ul class="social-list" data-reveal-stagger>
                            @foreach (config('portfolio.social') as $social)
                                <li>
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="social-pill">
                                        <x-icon :name="$social['icon']" /> {{ $social['name'] }}
                                        <x-icon name="arrow-up-right" :size="14" class="social-pill__arrow" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="col-lg-7">
                    <div class="card form-card" data-reveal>
                        <div class="form-card__head">
                            <div>
                                <h2 class="form-card__title"><x-rich :text="$p['form']['title']" /></h2>
                                <p class="form-card__sub">{{ $p['form']['subtitle'] }}</p>
                            </div>
                            <span class="icon-badge icon-badge--xl icon-badge--accent" aria-hidden="true"><x-icon name="send" :size="28" /></span>
                        </div>

                        <div class="form-alert form-alert--success" role="status" data-form-success @unless (session('contact_success')) hidden @endunless>
                            <x-icon name="check" /> <span data-form-success-text>{{ session('contact_success') }}</span>
                        </div>
                        <div class="form-alert form-alert--error" role="alert" data-form-error @unless (session('contact_error') || $errors->any()) hidden @endunless>
                            <x-icon name="alert" />
                            <span data-form-error-text>{{ session('contact_error') ?: ($errors->any() ? 'Please fix the highlighted fields and try again.' : '') }}</span>
                        </div>

                        <form method="POST" action="{{ route('contact.store') }}" class="contact-form" data-contact-form novalidate>
                            @csrf
                            <div class="hp-field" aria-hidden="true">
                                <label for="website">Leave this field empty</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="row g-3">
                                @foreach ([
                                    ['name' => 'name', 'label' => 'Your name', 'type' => 'text', 'auto' => 'name', 'placeholder' => 'Full name', 'max' => 100, 'col' => 'col-md-6', 'icon' => 'users'],
                                    ['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'auto' => 'email', 'placeholder' => 'you@company.com', 'max' => 150, 'col' => 'col-md-6', 'icon' => 'mail'],
                                    ['name' => 'subject', 'label' => 'Subject', 'type' => 'text', 'auto' => 'off', 'placeholder' => 'e.g. Meta Ads for my business', 'max' => 150, 'col' => 'col-12', 'icon' => 'megaphone'],
                                ] as $field)
                                    <div class="{{ $field['col'] }}">
                                        <div @class(['field', 'is-invalid' => $errors->has($field['name'])]) data-field="{{ $field['name'] }}">
                                            <label for="cf-{{ $field['name'] }}">{{ $field['label'] }}</label>
                                            <div class="field__control">
                                                <x-icon :name="$field['icon']" :size="18" class="field__icon" />
                                                <input type="{{ $field['type'] }}" id="cf-{{ $field['name'] }}" name="{{ $field['name'] }}"
                                                    value="{{ old($field['name'], $field['name'] === 'subject' ? request('subject') : '') }}"
                                                    placeholder="{{ $field['placeholder'] }}" autocomplete="{{ $field['auto'] }}" maxlength="{{ $field['max'] }}" required
                                                    aria-describedby="cf-{{ $field['name'] }}-error" @error($field['name']) aria-invalid="true" @enderror>
                                            </div>
                                            <p class="field__error" id="cf-{{ $field['name'] }}-error" data-field-error>@error($field['name']){{ $message }}@enderror</p>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-12">
                                    <div @class(['field', 'is-invalid' => $errors->has('message')]) data-field="message">
                                        <label for="cf-message">Message</label>
                                        <textarea id="cf-message" name="message" rows="6" maxlength="5000" required placeholder="Tell me about your business, goals and timeline…" aria-describedby="cf-message-error" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                                        <p class="field__error" id="cf-message-error" data-field-error>@error('message'){{ $message }}@enderror</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-foot">
                                <button type="submit" class="btn btn--primary btn--lg" data-submit>
                                    <span class="btn__label">{{ $p['form']['button'] }}</span>
                                    <span class="btn__spinner" aria-hidden="true"></span>
                                    <x-icon name="send" />
                                </button>
                                <p class="form-note"><x-icon name="check" :size="14" /> {{ $p['form']['note'] }}</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
