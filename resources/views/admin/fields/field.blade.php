{{--
    Renders one schema field (recursively for groups and repeaters).
    $field  schema entry        $value  current value
    $name   input name, e.g. data[hero][title]
    $path   dot path, e.g. data.hero.title (errors + uploads under files.*)
    $depth  repeater nesting level (template placeholders are __K{depth}__)
--}}
@php
    use App\Support\ContentForm;

    $type = $field['type'];
    $id = 'f-'.str_replace(['.', '[', ']'], '-', $path);
    $error = $errors->first($path);
    $label = $field['label'] ?? null;
    $help = $field['help'] ?? null;
    $fileName = 'files'.substr($name, 4); // data[a][b] → files[a][b]
@endphp

@switch($type)
    {{-- ======================= GROUP (section root) ======================= --}}
    @case('group')
        <div class="ad-fields">
            @foreach ($field['fields'] as $sub)
                <div @class(['ad-fields__cell', 'is-half' => ! empty($sub['half']), 'is-wide' => in_array($sub['type'], ['repeater', 'list', 'numbers'])])>
                    @include('admin.fields.field', ['field' => $sub, 'value' => $value[$sub['name']] ?? null, 'name' => "{$name}[{$sub['name']}]", 'path' => "{$path}.{$sub['name']}", 'depth' => $depth])
                </div>
            @endforeach
        </div>
        @break

    {{-- ======================= REPEATER ======================= --}}
    @case('repeater')
        @php($placeholder = "__K{$depth}__")
        <div class="ad-repeater" data-repeater data-placeholder="{{ $placeholder }}" data-item-label="{{ $field['item_label'] ?? '' }}">
            @if ($label && $path !== 'data')
                <div class="ad-repeater__label">
                    <span class="ad-label">{{ $label }}</span>
                    <span class="ad-repeater__count" data-repeater-count>{{ count((array) $value) }}</span>
                </div>
                @if ($help)<p class="ad-help">{{ $help }}</p>@endif
            @endif
            @if ($error)<p class="ad-error">{{ $error }}</p>@endif

            <div class="ad-repeater__items" data-repeater-items>
                @foreach ((array) $value as $key => $item)
                    @include('admin.fields.repeater-item', ['field' => $field, 'item' => (array) $item, 'key' => $key, 'name' => $name, 'path' => $path, 'depth' => $depth, 'collapsed' => count((array) $value) > 2])
                @endforeach
            </div>
            <template data-repeater-template>
                @include('admin.fields.repeater-item', ['field' => $field, 'item' => [], 'key' => $placeholder, 'name' => $name, 'path' => $path, 'depth' => $depth, 'collapsed' => false])
            </template>
            <button type="button" class="ad-add" data-repeater-add><x-icon name="plus" :size="16" /> Add {{ \Illuminate\Support\Str::singular(strtolower($label ?? 'item')) }}</button>
        </div>
        @break

    {{-- ======================= LIST / NUMBERS ======================= --}}
    @case('list')
    @case('numbers')
        @php($multiline = ! empty($field['multiline']))
        <div @class(['ad-list', 'ad-list--numbers' => $type === 'numbers']) data-list>
            @if ($label)<span class="ad-label">{{ $label }}</span>@endif
            @if ($help)<p class="ad-help">{{ $help }}</p>@endif
            <div class="ad-list__items" data-list-items>
                @foreach ((array) $value as $entry)
                    @include('admin.fields.list-item', ['name' => $name, 'value' => $entry, 'type' => $type, 'multiline' => $multiline])
                @endforeach
            </div>
            <template data-list-template>
                @include('admin.fields.list-item', ['name' => $name, 'value' => '', 'type' => $type, 'multiline' => $multiline])
            </template>
            <button type="button" class="ad-add ad-add--sm" data-list-add><x-icon name="plus" :size="14" /> Add {{ $type === 'numbers' ? 'number' : 'item' }}</button>
            @if ($error)<p class="ad-error">{{ $error }}</p>@endif
        </div>
        @break

    {{-- ======================= IMAGE / VIDEO ======================= --}}
    @case('image')
    @case('video')
        @php($isVideo = $type === 'video')
        <div @class(['ad-field', 'is-invalid' => $error]) data-media data-media-type="{{ $type }}">
            <span class="ad-label">{{ $label }}</span>
            <div @class(['ad-media', 'is-empty' => ! $value])>
                <div class="ad-media__preview" data-media-preview>
                    @if ($value)
                        @if ($isVideo)
                            <video src="{{ asset($value) }}" muted preload="metadata"></video>
                        @else
                            <img src="{{ asset($value) }}" alt="">
                        @endif
                    @endif
                    <span class="ad-media__empty"><x-icon :name="$isVideo ? 'play' : 'image'" :size="26" /> No {{ $isVideo ? 'video' : 'image' }}</span>
                </div>
                <div class="ad-media__side">
                    <p class="ad-media__path" data-media-path>{{ $value ? basename($value) : 'Nothing uploaded yet' }}</p>
                    <div class="ad-media__actions">
                        <label class="ad-btn ad-btn--ghost ad-btn--sm ad-media__pick">
                            <x-icon name="upload" :size="16" /> {{ $value ? 'Replace' : 'Upload' }}
                            <input type="file" name="{{ $fileName }}" accept="{{ $isVideo ? 'video/mp4,video/webm' : 'image/jpeg,image/png,image/webp,image/gif' }}" data-media-input>
                        </label>
                        @if (! empty($field['optional']))
                            <button type="button" class="ad-btn ad-btn--ghost ad-btn--sm ad-btn--danger" data-media-remove @if (! $value) hidden @endif><x-icon name="trash" :size="16" /> Remove</button>
                        @endif
                    </div>
                    <p class="ad-help">{{ $help ? $help.' ' : '' }}{{ $isVideo ? 'MP4 or WebM.' : 'JPG, PNG, WebP or GIF — resized and optimised automatically.' }}</p>
                </div>
            </div>
            <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-media-value>
            @if ($error)<p class="ad-error">{{ $error }}</p>@endif
        </div>
        @break

    {{-- ======================= TOGGLE ======================= --}}
    @case('toggle')
        <div class="ad-field ad-field--toggle">
            <input type="hidden" name="{{ $name }}" value="0">
            <label class="ad-switch" for="{{ $id }}">
                <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($value)>
                <span class="ad-switch__track"><span class="ad-switch__thumb"></span></span>
                <span class="ad-switch__label">{{ $label }}</span>
            </label>
        </div>
        @break

    {{-- ======================= SELECT / ICON ======================= --}}
    @case('select')
    @case('icon')
        @php($options = ContentForm::options($field))
        <div @class(['ad-field', 'is-invalid' => $error])>
            <label class="ad-label" for="{{ $id }}">{{ $label }}</label>
            <div @class(['ad-select', 'ad-select--icon' => $type === 'icon'])>
                @if ($type === 'icon')
                    <span class="ad-select__preview" data-icon-preview>@if ($value)<x-icon :name="$value" :size="18" />@endif</span>
                @endif
                <select id="{{ $id }}" name="{{ $name }}" @if ($type === 'icon') data-icon-select @endif>
                    @if (! array_key_exists((string) $value, $options) && $value !== null && $value !== '')
                        <option value="{{ $value }}" selected>{{ $value }}</option>
                    @endif
                    @foreach ($options as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" :size="16" class="ad-select__caret" />
            </div>
            @if ($help)<p class="ad-help">{{ $help }}</p>@endif
            @if ($error)<p class="ad-error">{{ $error }}</p>@endif
        </div>
        @break

    {{-- ======================= TEXT-LIKE ======================= --}}
    @default
        <div @class(['ad-field', 'is-invalid' => $error])>
            <label class="ad-label" for="{{ $id }}">{{ $label }}</label>
            @if (in_array($type, ['textarea', 'rich']))
                <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $field['rows'] ?? 2 }}" placeholder="{{ $field['placeholder'] ?? '' }}" data-autosize @if ($type === 'rich') data-rich @endif>{{ $value }}</textarea>
            @else
                <input id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
                    type="{{ ['number' => 'number', 'email' => 'email', 'url' => 'url'][$type] ?? 'text' }}"
                    @if ($type === 'number') step="{{ ! empty($field['integer']) ? '1' : 'any' }}" @endif
                    placeholder="{{ $field['placeholder'] ?? '' }}">
            @endif
            @if ($type === 'rich')
                <p class="ad-rich-preview" data-rich-preview aria-hidden="true"></p>
            @endif
            @if ($help)<p class="ad-help">{{ $help }}</p>@endif
            @if ($error)<p class="ad-error">{{ $error }}</p>@endif
        </div>
@endswitch
