<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Support\ContentRepository;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(ContentRepository $content): View
    {
        $pages = collect(config('admin.pages'));
        $edited = $content->editedAt($pages->flatMap(fn ($page) => array_column($page['sections'], 'key'))->all());

        return view('admin.dashboard', [
            'stats' => [
                'messages' => ContactMessage::query()->count(),
                'unread' => ContactMessage::query()->unread()->count(),
                'thisWeek' => ContactMessage::query()->where('created_at', '>=', now()->subWeek())->count(),
                'customized' => count($edited),
                'sections' => $pages->sum(fn ($page) => count($page['sections'])),
            ],
            'recentMessages' => ContactMessage::query()->latest()->limit(5)->get(),
            'recentEdits' => Content::query()->latest('updated_at')->limit(6)->get(['key', 'updated_at'])
                ->map(fn (Content $row) => ['row' => $row] + $this->locate($row->key)),
            'pages' => $pages->map(fn ($page, $slug) => $page + [
                'slug' => $slug,
                'edited' => collect($page['sections'])->filter(fn ($s) => isset($edited[$s['key']]))->count(),
            ]),
        ]);
    }

    /** Which admin page/section a content key belongs to. */
    private function locate(string $key): array
    {
        foreach (config('admin.pages') as $slug => $page) {
            foreach ($page['sections'] as $index => $section) {
                if ($section['key'] === $key) {
                    return ['page' => $slug, 'pageLabel' => $page['label'], 'section' => $index, 'sectionTitle' => $section['title']];
                }
            }
        }

        return ['page' => null, 'pageLabel' => null, 'section' => null, 'sectionTitle' => $key];
    }
}
