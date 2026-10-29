@props(['eyebrow', 'title', 'center' => false])
{{-- Wrap words in *asterisks* in the title to give them the gradient accent. --}}
<div {{ $attributes->class(['section-head', 'section-head--center' => $center]) }} data-reveal>
    <p class="eyebrow"><span class="eyebrow__line" aria-hidden="true"></span>{{ $eyebrow }}</p>
    <h2 class="section-title"><x-rich :text="$title" /></h2>
    @if ($slot->isNotEmpty())
        <p class="section-lead">{{ $slot }}</p>
    @endif
</div>
