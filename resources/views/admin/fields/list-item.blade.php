<div class="ad-list__item" data-list-item>
    <span class="ad-list__handle" aria-hidden="true"></span>
    @if ($multiline)
        <textarea name="{{ $name }}[]" rows="2" data-autosize>{{ $value }}</textarea>
    @else
        <input type="{{ $type === 'numbers' ? 'number' : 'text' }}" @if ($type === 'numbers') step="any" @endif name="{{ $name }}[]" value="{{ $value }}">
    @endif
    <div class="ad-list__tools">
        <button type="button" class="ad-icon-btn ad-icon-btn--xs" data-list-up aria-label="Move up"><x-icon name="chevron-up" :size="14" /></button>
        <button type="button" class="ad-icon-btn ad-icon-btn--xs" data-list-down aria-label="Move down"><x-icon name="chevron-down" :size="14" /></button>
        <button type="button" class="ad-icon-btn ad-icon-btn--xs ad-icon-btn--danger" data-list-remove aria-label="Remove"><x-icon name="close" :size="14" /></button>
    </div>
</div>
