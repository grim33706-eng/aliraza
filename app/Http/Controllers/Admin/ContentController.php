<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ContentForm;
use App\Support\ContentRepository;
use App\Support\MediaStore;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Edits one website page, section by section, following config/admin.php.
 */
class ContentController extends Controller
{
    public function __construct(private ContentRepository $content) {}

    public function edit(string $page): View
    {
        $schema = $this->page($page);
        $edited = $this->content->editedAt(array_column($schema['sections'], 'key'));

        return view('admin.pages.edit', [
            'slug' => $page,
            'page' => $schema,
            'sections' => collect($schema['sections'])->map(fn ($section, $index) => $section + [
                'index' => $index,
                'value' => $this->content->get($section['key']),
                'edited_at' => $edited[$section['key']] ?? null,
            ]),
            'maxUpload' => MediaStore::maxUploadBytes(),
        ]);
    }

    public function update(Request $request, ContentForm $form, MediaStore $media, string $page, int $section): RedirectResponse
    {
        $schema = $this->section($page, $section);

        try {
            $value = $form->fromRequest($schema, $request, $this->content->get($schema['key']));
        } catch (ValidationException $e) {
            // Keep what was typed, and reopen this section with its errors.
            return back()->withErrors($e->errors())->withInput()->with('open_section', $section)
                ->withFragment("section-{$section}");
        }

        $this->content->save($schema['key'], $value);
        foreach ($form->replacedUploads() as $old) {
            $media->delete($old);
        }

        return to_route('admin.pages.edit', $page)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Changes saved',
                'message' => "“{$schema['title']}” is now live on the website.",
            ])
            ->with('open_section', $section)
            ->withFragment("section-{$section}");
    }

    public function reset(string $page, int $section): RedirectResponse
    {
        $schema = $this->section($page, $section);
        $this->content->reset($schema['key']);

        return to_route('admin.pages.edit', $page)
            ->with('toast', [
                'type' => 'info', 'icon' => 'reset',
                'title' => 'Original restored',
                'message' => "“{$schema['title']}” is back to its original content.",
            ])
            ->with('open_section', $section)
            ->withFragment("section-{$section}");
    }

    private function page(string $page): array
    {
        return config("admin.pages.{$page}") ?? abort(404);
    }

    private function section(string $page, int $section): array
    {
        return $this->page($page)['sections'][$section] ?? abort(404);
    }
}
