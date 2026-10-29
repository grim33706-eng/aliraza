@php
    $itemName = "{$name}[{$key}]";
    $itemPath = "{$path}.{$key}";
    $title = ! empty($field['item_label']) ? ($item[$field['item_label']] ?? '') : '';
    // Keep items with errors open so problems are visible.
    $hasErrors = collect($errors->keys())->contains(fn ($k) => str_starts_with($k, "{$itemPath}."));
@endphp
<div @class(['ad-repeater__item', 'is-collapsed' => $collapsed && ! $hasErrors, 'has-errors' => $hasErrors]) data-repeater-item>
    <div class="ad-repeater__head">
        <button type="button" class="ad-repeater__toggle" data-item-toggle aria-expanded="{{ $collapsed && ! $hasErrors ? 'false' : 'true' }}">
            <span class="ad-repeater__index" data-item-index></span>
            <span class="ad-repeater__title" data-item-title>{{ is_scalar($title) && $title !== '' ? $title : 'New item' }}</span>
            <x-icon name="chevron-down" :size="16" class="ad-repeater__chevron" />
        </button>
        <div class="ad-repeater__tools">
            <button type="button" class="ad-icon-btn ad-icon-btn--sm" data-item-up aria-label="Move up" title="Move up"><x-icon name="chevron-up" :size="16" /></button>
            <button type="button" class="ad-icon-btn ad-icon-btn--sm" data-item-down aria-label="Move down" title="Move down"><x-icon name="chevron-down" :size="16" /></button>
            <button type="button" class="ad-icon-btn ad-icon-btn--sm ad-icon-btn--danger" data-item-remove aria-label="Remove" title="Remove"><x-icon name="trash" :size="16" /></button>
        </div>
    </div>
    <div class="ad-repeater__body">
        @include('admin.fields.field', ['field' => ['type' => 'group', 'fields' => $field['fields']], 'value' => $item, 'name' => $itemName, 'path' => $itemPath, 'depth' => $depth + 1])
    </div>
</div>
