@php
    use App\Support\ContentForm;

    $openSection = session('open_section');
@endphp
<x-admin.layout :title="$page['label']" :heading="$page['label'].($page['route'] ? ' page' : '')" :subheading="$page['description']">
    @if ($page['route'])
        <x-slot:actions>
            <a href="{{ route($page['route']) }}" class="ad-btn ad-btn--ghost" target="_blank" rel="noopener"><x-icon name="external" :size="16" /> View page</a>
        </x-slot:actions>
    @endif

    <div class="ad-editor">
        {{-- ============ SECTION INDEX ============ --}}
        <nav class="ad-editor__toc" aria-label="Sections on this page">
            <p class="ad-editor__toc-title">On this page</p>
            <ol>
                @foreach ($sections as $section)
                    <li>
                        <a href="#section-{{ $section['index'] }}" data-section-link="{{ $section['index'] }}">
                            <span @class(['ad-toc-dot', 'is-edited' => $section['edited_at']])></span>
                            {{ $section['title'] }}
                        </a>
                    </li>
                @endforeach
            </ol>
            <p class="ad-editor__legend"><span class="ad-toc-dot is-edited"></span> Edited &nbsp; <span class="ad-toc-dot"></span> Original</p>
        </nav>

        {{-- ============ SECTIONS ============ --}}
        <div class="ad-editor__sections">
            <div class="ad-tip">
                <x-icon name="sparkles" :size="18" />
                <p>Each section saves on its own and goes live straight away. In headings, wrap words in <code>*asterisks*</code> for the lime-to-violet highlight.</p>
            </div>

            @foreach ($sections as $section)
                @php
                    $i = $section['index'];
                    $isOpen = $openSection === $i || ($openSection === null && $loop->first);
                    $value = ($openSection === $i && old('data') !== null) ? old('data') : $section['value'];
                    $root = ContentForm::rootField($section);
                @endphp
                <section @class(['ad-section', 'is-open' => $isOpen, 'has-errors' => $openSection === $i && $errors->any()]) id="section-{{ $i }}" data-section>
                    <button type="button" class="ad-section__head" data-section-toggle aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="section-body-{{ $i }}">
                        <span class="ad-section__icon"><x-icon :name="$section['icon'] ?? 'pencil'" :size="20" /></span>
                        <span class="ad-section__titles">
                            <strong>{{ $section['title'] }}</strong>
                            @if (! empty($section['description']))<span>{{ $section['description'] }}</span>@endif
                        </span>
                        @if ($section['edited_at'])
                            <span class="ad-pill ad-pill--accent" title="{{ $section['edited_at']->format('j M Y, H:i') }}">Edited {{ $section['edited_at']->diffForHumans(short: true) }}</span>
                        @else
                            <span class="ad-pill">Original</span>
                        @endif
                        <span class="ad-section__chevron"><x-icon name="chevron-down" :size="18" /></span>
                    </button>

                    <div class="ad-section__body" id="section-body-{{ $i }}">
                        @if ($openSection === $i && $errors->any())
                            <div class="ad-alert ad-alert--error"><x-icon name="alert" :size="18" /> Please fix the highlighted fields below — nothing was saved yet.</div>
                        @endif

                        <form method="POST" action="{{ route('admin.pages.update', [$slug, $i]) }}" enctype="multipart/form-data" class="ad-form" data-content-form novalidate>
                            @csrf
                            @method('PUT')
                            @include('admin.fields.field', ['field' => $root, 'value' => $value, 'name' => 'data', 'path' => 'data', 'depth' => 0])

                            <footer class="ad-section__foot">
                                <span class="ad-dirty" data-dirty hidden><span class="ad-dirty__dot"></span> Unsaved changes</span>
                                @if ($section['edited_at'])
                                    <button type="submit" form="reset-{{ $i }}" class="ad-btn ad-btn--ghost ad-btn--sm"><x-icon name="reset" :size="16" /> Restore original</button>
                                @endif
                                <button type="submit" class="ad-btn ad-btn--primary" data-save><x-icon name="save" :size="16" /> <span>Save section</span></button>
                            </footer>
                        </form>
                        @if ($section['edited_at'])
                            <form method="POST" action="{{ route('admin.pages.reset', [$slug, $i]) }}" id="reset-{{ $i }}" data-confirm-title="Restore the original?" data-confirm-label="Restore original" data-confirm-tone="warn" data-confirm-icon="reset" data-confirm="Restore “{{ $section['title'] }}” to the original content? Your edits to this section will be removed." hidden>
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    {{-- Icon artwork for live previews in icon pickers --}}
    <div hidden data-icon-library>
        @foreach (config('admin.icons') as $icon => $label)
            <span data-icon="{{ $icon }}"><x-icon :name="$icon" :size="18" /></span>
        @endforeach
    </div>
    <script>window.adminMaxUpload = {{ $maxUpload }};</script>
</x-admin.layout>
