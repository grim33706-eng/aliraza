@props(['inlineErrors' => false])
@php
    // Flashed notifications, rendered by admin.js (which also adds its own for
    // in-page actions like adding or removing items).
    $toasts = [];
    if (session('toast')) $toasts[] = session('toast');
    if (session('status')) $toasts[] = ['type' => 'success', 'title' => session('status')];
    if (session('error')) $toasts[] = ['type' => 'error', 'title' => session('error')];
    if (! $inlineErrors && collect($errors->getBags())->contains(fn ($bag) => $bag->any())) {
        $toasts[] = ['type' => 'error', 'title' => 'Couldn\'t save your changes', 'message' => 'Please fix the highlighted fields and try again.'];
    }
@endphp
<div class="ad-toasts" aria-live="polite" data-toasts></div>
<script type="application/json" data-toast-data>@json($toasts)</script>
<template data-ui-icons>
    @foreach (['check', 'trash', 'alert', 'sparkles', 'logout', 'reset', 'plus', 'mail-open', 'user', 'lock', 'image', 'upload', 'layers', 'close'] as $name)
        <span data-icon="{{ $name }}"><x-icon :name="$name" :size="18" /></span>
    @endforeach
</template>
