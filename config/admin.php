<?php

/*
|--------------------------------------------------------------------------
| Admin panel content schema
|--------------------------------------------------------------------------
|
| Describes every editable page of the website, section by section, in the
| same order the sections appear on the site. Each section edits one key of
| config/portfolio.php (its "key"); the admin's version is stored in the
| `contents` table. Section keys must not overlap (none is a prefix of another).
|
| Section "type": group (default, named fields) | repeater (list of items) |
|                 list (list of strings) | numbers (list of numbers).
|
| Field types: text, textarea, rich (text with *accent* / **bold**), url,
| email, number, toggle, select, icon, image, video, list, numbers, repeater.
| Options: label, help, placeholder, rows, half (two-per-row), integer,
| options (select), options_from (dot-path + ".*.field" for dynamic selects),
| fields + item_label (repeater), dimensions (image: sibling width/height),
| multiline (list items as textareas), optional (image may be removed).
|
*/

$icons = [
    'megaphone' => 'Megaphone', 'calendar' => 'Calendar', 'sparkles' => 'Sparkles', 'target' => 'Target',
    'search' => 'Search', 'play' => 'Play', 'funnel' => 'Funnel', 'flask' => 'Flask', 'chart' => 'Chart',
    'trending' => 'Trending', 'users' => 'Users', 'layers' => 'Layers', 'globe' => 'Globe',
    'graduation' => 'Graduation cap', 'wallet' => 'Wallet', 'briefcase' => 'Briefcase', 'pin' => 'Map pin',
    'clock' => 'Clock', 'mail' => 'Mail', 'phone' => 'Phone', 'send' => 'Send', 'check' => 'Check',
    'home' => 'Home', 'image' => 'Image', 'chat' => 'Chat', 'facebook' => 'Facebook', 'instagram' => 'Instagram',
    'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp',
];

$titleHelp = 'Wrap words in *asterisks* to give them the lime-to-violet gradient.';
$richHelp = 'Use *asterisks* for the gradient accent and **double asterisks** for bold.';

$heading = fn (bool $lead = false) => array_filter([
    ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Eyebrow', 'help' => 'Small label above the heading.', 'half' => true],
    ['name' => 'title', 'type' => 'rich', 'label' => 'Heading', 'help' => $titleHelp],
    $lead ? ['name' => 'lead', 'type' => 'textarea', 'label' => 'Intro text', 'rows' => 3] : null,
]);

$hero = fn (array $extra = []) => [
    'title' => 'Hero banner',
    'icon' => 'sparkles',
    'description' => 'The full-screen banner at the top of the page.',
    'fields' => array_merge([
        ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Eyebrow', 'half' => true],
        ['name' => 'watermark', 'type' => 'text', 'label' => 'Background word', 'help' => 'The giant outlined word behind the banner.', 'half' => true],
        ['name' => 'title', 'type' => 'rich', 'label' => 'Headline', 'help' => $titleHelp],
        ['name' => 'lead', 'type' => 'textarea', 'label' => 'Intro text', 'rows' => 3],
    ], $extra),
];

$seo = fn (string $page) => [
    'key' => "pages.{$page}.seo",
    'title' => 'Search & sharing (SEO)',
    'icon' => 'search',
    'description' => 'What Google and link previews show for this page. Leave blank to use the defaults.',
    'fields' => [
        ['name' => 'title', 'type' => 'text', 'label' => 'Page title', 'help' => '"· Raza Sandhu" is added automatically.'],
        ['name' => 'description', 'type' => 'textarea', 'label' => 'Meta description', 'rows' => 3, 'help' => 'About 150 characters works best.'],
    ],
];

$iconCard = [
    ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
    ['name' => 'title', 'type' => 'text', 'label' => 'Title', 'half' => true],
    ['name' => 'text', 'type' => 'textarea', 'label' => 'Text', 'rows' => 2],
];

return [

    'icons' => $icons,

    'pages' => [

        /* ------------------------------------------------------------------ */
        'home' => [
            'label' => 'Home',
            'icon' => 'home',
            'route' => 'home',
            'description' => 'The landing page: hero, previews of every section and the call to action.',
            'sections' => [
                ['key' => 'pages.home.hero', 'title' => 'Hero banner', 'icon' => 'sparkles', 'description' => 'Headline, intro, buttons and the floating figures on the right.', 'fields' => [
                    ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Availability badge'],
                    ['name' => 'title', 'type' => 'rich', 'label' => 'Headline', 'help' => $titleHelp],
                    ['name' => 'lead', 'type' => 'rich', 'label' => 'Intro text', 'rows' => 3, 'help' => $richHelp],
                    ['name' => 'primary_label', 'type' => 'text', 'label' => 'Main button', 'half' => true],
                    ['name' => 'secondary_label', 'type' => 'text', 'label' => 'Second button', 'half' => true],
                    ['name' => 'platforms', 'type' => 'repeater', 'label' => 'Platform badges', 'item_label' => 'label', 'fields' => [
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
                        ['name' => 'label', 'type' => 'text', 'label' => 'Label', 'half' => true],
                    ]],
                    ['name' => 'kpis', 'type' => 'repeater', 'label' => 'Floating figures', 'help' => 'The glass cards that count up beside the 3D scene.', 'item_label' => 'label', 'fields' => [
                        ['name' => 'label', 'type' => 'text', 'label' => 'Label', 'half' => true],
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
                        ['name' => 'value', 'type' => 'number', 'label' => 'Value', 'half' => true],
                        ['name' => 'decimals', 'type' => 'number', 'label' => 'Decimal places', 'integer' => true, 'half' => true],
                        ['name' => 'prefix', 'type' => 'text', 'label' => 'Before the number', 'placeholder' => 'e.g. Rs ', 'half' => true],
                        ['name' => 'suffix', 'type' => 'text', 'label' => 'After the number', 'placeholder' => 'e.g. %', 'half' => true],
                    ]],
                    ['name' => 'kpi_note', 'type' => 'text', 'label' => 'Note under the figures'],
                    ['name' => 'meta_strong', 'type' => 'text', 'label' => 'Bottom strip — highlighted', 'half' => true],
                    ['name' => 'meta_text', 'type' => 'text', 'label' => 'Bottom strip — text', 'half' => true],
                ]],
                ['key' => 'pages.home.marquee', 'title' => 'Scrolling text band', 'icon' => 'layers', 'description' => 'The second (outlined) row. The first row lists the tools from Services → Toolkit.', 'fields' => [
                    ['name' => 'second_row', 'type' => 'list', 'label' => 'Outlined row items'],
                ]],
                ['key' => 'pages.home.about', 'title' => 'About preview', 'icon' => 'users', 'description' => 'Photo and first intro paragraph come from About → Profile.', 'fields' => array_merge($heading(), [
                    ['name' => 'link_label', 'type' => 'text', 'label' => 'Link text', 'half' => true],
                    ['name' => 'badge_text', 'type' => 'text', 'label' => 'Text beside the years badge', 'half' => true],
                    ['name' => 'chips', 'type' => 'repeater', 'label' => 'Floating labels around the photo', 'item_label' => 'label', 'fields' => [
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
                        ['name' => 'label', 'type' => 'text', 'label' => 'Label', 'half' => true],
                    ]],
                ])],
                ['key' => 'pages.home.services', 'title' => 'Services preview', 'icon' => 'megaphone', 'description' => 'Shows the first four services from the Services page.', 'fields' => array_merge($heading(), [
                    ['name' => 'link_label', 'type' => 'text', 'label' => 'Link text', 'help' => ':count is replaced by the number of services.', 'half' => true],
                    ['name' => 'cta_button', 'type' => 'text', 'label' => 'Green card button', 'half' => true],
                    ['name' => 'cta_text', 'type' => 'text', 'label' => 'Green card text'],
                ])],
                ['key' => 'pages.home.portfolio', 'title' => 'Portfolio preview', 'icon' => 'image', 'description' => 'Shows the first two items with screenshots from the Portfolio page.', 'fields' => array_merge($heading(), [
                    ['name' => 'link_label', 'type' => 'text', 'label' => 'Link text'],
                ])],
                ['key' => 'pages.home.results', 'title' => 'Results preview', 'icon' => 'chart', 'description' => 'Counters come from Results → Career counters.', 'fields' => array_merge($heading(), [
                    ['name' => 'link_label', 'type' => 'text', 'label' => 'Link text'],
                ])],
                ['key' => 'pages.home.international', 'title' => 'International preview', 'icon' => 'globe', 'description' => 'The globe and country chips come from the International page.', 'fields' => array_merge($heading(true), [
                    ['name' => 'link_label', 'type' => 'text', 'label' => 'Link text'],
                ])],
                $seo('home'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'about' => [
            'label' => 'About',
            'icon' => 'users',
            'route' => 'about',
            'description' => 'Profile, strengths, career timeline, skills and education.',
            'sections' => [
                ['key' => 'pages.about.hero'] + $hero(),
                ['key' => 'profile', 'title' => 'Profile', 'icon' => 'users', 'description' => 'Used across the whole site — photo, name, job title and introduction.', 'fields' => [
                    ['name' => 'photo', 'type' => 'image', 'label' => 'Profile photo', 'help' => 'Portrait works best (at least 800px wide).'],
                    ['name' => 'name', 'type' => 'text', 'label' => 'Full name', 'half' => true],
                    ['name' => 'location', 'type' => 'text', 'label' => 'Location', 'half' => true],
                    ['name' => 'title', 'type' => 'text', 'label' => 'Job title'],
                    ['name' => 'short_title', 'type' => 'text', 'label' => 'Short job title', 'half' => true],
                    ['name' => 'years', 'type' => 'text', 'label' => 'Years of experience', 'placeholder' => '2+', 'half' => true],
                    ['name' => 'summary', 'type' => 'textarea', 'label' => 'One-paragraph summary', 'rows' => 3, 'help' => 'Also used as the default description for search engines.'],
                    ['name' => 'intro', 'type' => 'list', 'label' => 'Introduction paragraphs', 'multiline' => true, 'help' => 'The first paragraph also appears on the Home page.'],
                    ['name' => 'strengths', 'type' => 'repeater', 'label' => 'Strengths ("How I work" cards)', 'item_label' => 'title', 'fields' => $iconCard],
                ]],
                ['key' => 'pages.about.intro', 'title' => 'Introduction section', 'icon' => 'briefcase', 'description' => 'Heading, buttons and the four fact tiles under the photo.', 'fields' => array_merge($heading(), [
                    ['name' => 'primary_label', 'type' => 'text', 'label' => 'Main button', 'half' => true],
                    ['name' => 'secondary_label', 'type' => 'text', 'label' => 'LinkedIn button', 'half' => true],
                    ['name' => 'facts', 'type' => 'repeater', 'label' => 'Fact tiles', 'item_label' => 'label', 'fields' => [
                        ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
                        ['name' => 'label', 'type' => 'text', 'label' => 'Label', 'half' => true],
                        ['name' => 'value', 'type' => 'text', 'label' => 'Value'],
                    ]],
                ])],
                ['key' => 'pages.about.strengths', 'title' => 'Strengths heading', 'icon' => 'flask', 'description' => 'The cards themselves are edited under Profile.', 'fields' => $heading()],
                ['key' => 'pages.about.experience', 'title' => 'Experience heading', 'icon' => 'clock', 'fields' => $heading(true)],
                ['key' => 'experience', 'type' => 'repeater', 'title' => 'Career timeline', 'icon' => 'briefcase', 'description' => 'Newest role first. Also drives the milestones on the Results page.', 'item_label' => 'role', 'fields' => [
                    ['name' => 'role', 'type' => 'text', 'label' => 'Role', 'half' => true],
                    ['name' => 'company', 'type' => 'text', 'label' => 'Company', 'half' => true],
                    ['name' => 'period', 'type' => 'text', 'label' => 'Period', 'placeholder' => 'Mar 2026 – Present', 'half' => true],
                    ['name' => 'mode', 'type' => 'select', 'label' => 'Work mode', 'options' => ['On-site' => 'On-site', 'Remote' => 'Remote', 'Hybrid' => 'Hybrid'], 'half' => true],
                    ['name' => 'current', 'type' => 'toggle', 'label' => 'Current role'],
                    ['name' => 'points', 'type' => 'list', 'label' => 'Responsibilities', 'multiline' => true],
                ]],
                ['key' => 'pages.about.skills', 'title' => 'Skills heading', 'icon' => 'sparkles', 'fields' => array_merge($heading(true), [
                    ['name' => 'hint', 'type' => 'text', 'label' => '3D sphere hint'],
                ])],
                ['key' => 'skills', 'type' => 'repeater', 'title' => 'Skill groups', 'icon' => 'layers', 'description' => 'Shown as chip lists and on the 3D skills sphere.', 'item_label' => 'name', 'fields' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Group name'],
                    ['name' => 'skills', 'type' => 'list', 'label' => 'Skills'],
                ]],
                ['key' => 'pages.about.education', 'title' => 'Education heading & seal', 'icon' => 'graduation', 'fields' => array_merge($heading(), [
                    ['name' => 'label', 'type' => 'text', 'label' => 'Degree label', 'half' => true],
                    ['name' => 'seal_text', 'type' => 'text', 'label' => 'Rotating seal text', 'help' => 'The years are added automatically.', 'half' => true],
                    ['name' => 'seal_small', 'type' => 'text', 'label' => 'Seal — small text', 'half' => true],
                    ['name' => 'seal_big', 'type' => 'text', 'label' => 'Seal — big text', 'half' => true],
                ])],
                ['key' => 'education', 'title' => 'Degree', 'icon' => 'graduation', 'description' => 'Only the Bachelor\'s degree is shown on the site.', 'fields' => [
                    ['name' => 'degree', 'type' => 'text', 'label' => 'Degree'],
                    ['name' => 'institution', 'type' => 'text', 'label' => 'Institution'],
                    ['name' => 'location', 'type' => 'text', 'label' => 'Location', 'half' => true],
                    ['name' => 'period', 'type' => 'text', 'label' => 'Years', 'placeholder' => '2021 – 2025', 'help' => 'Write it as "start – end" years.', 'half' => true],
                ]],
                $seo('about'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'services' => [
            'label' => 'Services',
            'icon' => 'megaphone',
            'route' => 'services',
            'description' => 'Service groups and cards, the process and the toolkit orbit.',
            'sections' => [
                ['key' => 'pages.services.hero'] + $hero([
                    ['name' => 'primary_label', 'type' => 'text', 'label' => 'Main button', 'half' => true],
                    ['name' => 'secondary_label', 'type' => 'text', 'label' => 'Second button', 'half' => true],
                ]),
                ['key' => 'service_groups', 'type' => 'repeater', 'title' => 'Service groups', 'icon' => 'layers', 'description' => 'Each group is a section of the page. Services are assigned to a group by name.', 'item_label' => 'name', 'fields' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Group name', 'half' => true],
                    ['name' => 'title', 'type' => 'rich', 'label' => 'Heading', 'help' => $titleHelp, 'half' => true],
                    ['name' => 'text', 'type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                ]],
                ['key' => 'services', 'type' => 'repeater', 'title' => 'Services', 'icon' => 'megaphone', 'description' => 'The first four also appear on the Home page.', 'item_label' => 'title', 'fields' => [
                    ['name' => 'title', 'type' => 'text', 'label' => 'Title', 'half' => true],
                    ['name' => 'group', 'type' => 'select', 'label' => 'Group', 'options_from' => 'service_groups.*.name', 'half' => true],
                    ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon', 'half' => true],
                    ['name' => 'slug', 'type' => 'text', 'label' => 'Link anchor', 'help' => 'Lowercase, dashes only (used in links like /services#meta-ads).', 'half' => true],
                    ['name' => 'text', 'type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                    ['name' => 'benefits', 'type' => 'list', 'label' => 'Benefits (tick list)'],
                ]],
                ['key' => 'pages.services.cards', 'title' => 'Card labels', 'icon' => 'check', 'fields' => [
                    ['name' => 'cta_label', 'type' => 'text', 'label' => 'Card button', 'half' => true],
                    ['name' => 'count_label', 'type' => 'text', 'label' => 'Word after the count', 'help' => 'e.g. "3 services"', 'half' => true],
                ]],
                ['key' => 'pages.services.process', 'title' => 'Process heading', 'icon' => 'trending', 'fields' => $heading(true)],
                ['key' => 'process', 'type' => 'repeater', 'title' => 'Process steps', 'icon' => 'trending', 'item_label' => 'title', 'fields' => [
                    ['name' => 'title', 'type' => 'text', 'label' => 'Step'],
                    ['name' => 'text', 'type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                ]],
                ['key' => 'pages.services.toolkit', 'title' => 'Toolkit heading', 'icon' => 'globe', 'fields' => $heading(true)],
                ['key' => 'tools', 'type' => 'list', 'title' => 'Tools & platforms', 'icon' => 'sparkles', 'description' => 'Shown in the 3D orbit and the scrolling band on the Home page.'],
                $seo('services'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'portfolio' => [
            'label' => 'Portfolio',
            'icon' => 'image',
            'route' => 'portfolio',
            'description' => 'Work items with screenshots and recordings, filters and the 3D evidence wall.',
            'sections' => [
                ['key' => 'pages.portfolio.hero'] + $hero(),
                ['key' => 'pages.portfolio.gallery', 'title' => '3D evidence wall', 'icon' => 'layers', 'description' => 'Shows every item that has a screenshot or recording.', 'fields' => [
                    ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Eyebrow', 'half' => true],
                    ['name' => 'hint', 'type' => 'text', 'label' => 'Scroll hint', 'half' => true],
                    ['name' => 'title', 'type' => 'rich', 'label' => 'Heading', 'help' => ':count is replaced by the number of captures. '.$titleHelp],
                ]],
                ['key' => 'pages.portfolio.grid', 'title' => 'Work grid heading', 'icon' => 'search', 'fields' => array_merge($heading(), [
                    ['name' => 'footnote', 'type' => 'textarea', 'label' => 'Footnote', 'rows' => 2],
                ])],
                ['key' => 'portfolio_filters', 'type' => 'repeater', 'title' => 'Filter buttons', 'icon' => 'funnel', 'description' => 'Keep the "all" filter first — it shows every item.', 'item_label' => 'label', 'fields' => [
                    ['name' => 'label', 'type' => 'text', 'label' => 'Button label', 'half' => true],
                    ['name' => 'key', 'type' => 'text', 'label' => 'Filter key', 'help' => 'Items list this key under "Filters".', 'half' => true],
                ]],
                ['key' => 'portfolio', 'type' => 'repeater', 'title' => 'Work items', 'icon' => 'image', 'description' => 'The first item is shown extra wide. Items with a screenshot or recording appear on the 3D wall.', 'item_label' => 'title', 'fields' => [
                    ['name' => 'title', 'type' => 'text', 'label' => 'Title'],
                    ['name' => 'client', 'type' => 'text', 'label' => 'Client / label', 'half' => true],
                    ['name' => 'market', 'type' => 'text', 'label' => 'Market', 'placeholder' => 'Optional', 'half' => true],
                    ['name' => 'media_type', 'type' => 'select', 'label' => 'Media', 'options' => ['image' => 'Screenshot', 'video' => 'Screen recording', 'none' => 'No media (icon card)'], 'half' => true],
                    ['name' => 'icon', 'type' => 'icon', 'label' => 'Icon (no-media cards)', 'half' => true],
                    ['name' => 'image', 'type' => 'image', 'label' => 'Screenshot / video poster', 'dimensions' => ['width', 'height'], 'optional' => true],
                    ['name' => 'video', 'type' => 'video', 'label' => 'Recording (MP4)', 'optional' => true],
                    ['name' => 'text', 'type' => 'textarea', 'label' => 'Description', 'rows' => 2],
                    ['name' => 'stats', 'type' => 'list', 'label' => 'Highlight pills'],
                    ['name' => 'tags', 'type' => 'list', 'label' => 'Tags'],
                    ['name' => 'filters', 'type' => 'list', 'label' => 'Filters', 'help' => 'Filter keys, e.g. evidence, lead-gen, social, paid.'],
                    ['name' => 'slug', 'type' => 'text', 'label' => 'Link anchor', 'help' => 'Lowercase, dashes only.', 'half' => true],
                    ['name' => 'width', 'type' => 'number', 'label' => 'Media width (px)', 'integer' => true, 'help' => 'Filled in automatically on upload.', 'half' => true],
                    ['name' => 'height', 'type' => 'number', 'label' => 'Media height (px)', 'integer' => true, 'half' => true],
                ]],
                $seo('portfolio'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'results' => [
            'label' => 'Results',
            'icon' => 'chart',
            'route' => 'results',
            'description' => 'Headline figures, both charts, career counters, achievements and sources.',
            'sections' => [
                ['key' => 'pages.results.hero'] + $hero(),
                ['key' => 'pages.results.highlights', 'title' => 'Headline figures heading', 'icon' => 'chart', 'fields' => $heading()],
                ['key' => 'results.highlights', 'type' => 'repeater', 'title' => 'Headline figures', 'icon' => 'trending', 'description' => 'The first two are shown large.', 'item_label' => 'label', 'fields' => [
                    ['name' => 'label', 'type' => 'text', 'label' => 'Label', 'half' => true],
                    ['name' => 'source', 'type' => 'select', 'label' => 'Source', 'options_from' => 'results.sources.*.key', 'half' => true],
                    ['name' => 'value', 'type' => 'number', 'label' => 'Value', 'half' => true],
                    ['name' => 'decimals', 'type' => 'number', 'label' => 'Decimal places', 'integer' => true, 'half' => true],
                    ['name' => 'prefix', 'type' => 'text', 'label' => 'Before the number', 'placeholder' => 'e.g. Rs ', 'half' => true],
                    ['name' => 'suffix', 'type' => 'text', 'label' => 'After the number', 'placeholder' => 'e.g. %', 'half' => true],
                ]],
                ['key' => 'pages.results.charts', 'title' => 'Chart headings', 'icon' => 'chart', 'fields' => array_merge($heading(), [
                    ['name' => 'leads_eyebrow', 'type' => 'text', 'label' => 'Bar chart — label', 'half' => true],
                    ['name' => 'leads_title', 'type' => 'text', 'label' => 'Bar chart — title', 'half' => true],
                    ['name' => 'leads_sub', 'type' => 'text', 'label' => 'Bar chart — subtitle', 'help' => ':count = number of bars, :total = their sum.'],
                    ['name' => 'ctr_eyebrow', 'type' => 'text', 'label' => '3D chart — label', 'half' => true],
                    ['name' => 'ctr_title', 'type' => 'text', 'label' => '3D chart — title', 'half' => true],
                    ['name' => 'ctr_sub', 'type' => 'text', 'label' => '3D chart — subtitle', 'help' => ':count = number of campaigns.'],
                ])],
                ['key' => 'results.leads_chart', 'type' => 'numbers', 'title' => 'Bar chart — leads per campaign', 'icon' => 'chart', 'description' => 'One number per bar, left to right.'],
                ['key' => 'results.ctr_chart', 'type' => 'repeater', 'title' => '3D chart — CTR per campaign', 'icon' => 'trending', 'item_label' => 'label', 'fields' => [
                    ['name' => 'label', 'type' => 'text', 'label' => 'Campaign', 'half' => true],
                    ['name' => 'value', 'type' => 'number', 'label' => 'CTR (%)', 'half' => true],
                ]],
                ['key' => 'pages.results.achievements', 'title' => 'Achievements heading', 'icon' => 'sparkles', 'fields' => $heading()],
                ['key' => 'results.career', 'type' => 'repeater', 'title' => 'Career counters', 'icon' => 'clock', 'description' => 'Also shown on the Home page.', 'item_label' => 'label', 'fields' => [
                    ['name' => 'label', 'type' => 'text', 'label' => 'Label'],
                    ['name' => 'value', 'type' => 'number', 'label' => 'Number', 'half' => true],
                    ['name' => 'suffix', 'type' => 'text', 'label' => 'After the number', 'placeholder' => 'e.g. +', 'half' => true],
                ]],
                ['key' => 'results.achievements', 'type' => 'repeater', 'title' => 'Achievement cards', 'icon' => 'trending', 'item_label' => 'title', 'fields' => $iconCard],
                ['key' => 'pages.results.milestones', 'title' => 'Milestones heading', 'icon' => 'trending', 'description' => 'The steps are built from About → Career timeline.', 'fields' => $heading(true)],
                ['key' => 'pages.results.sources', 'title' => 'Sources heading', 'icon' => 'search', 'fields' => array_merge($heading(), [
                    ['name' => 'footnote', 'type' => 'textarea', 'label' => 'Footnote', 'rows' => 3],
                ])],
                ['key' => 'results.sources', 'type' => 'repeater', 'title' => 'Source screenshots', 'icon' => 'image', 'item_label' => 'label', 'fields' => [
                    ['name' => 'key', 'type' => 'text', 'label' => 'Short key', 'placeholder' => 'A', 'half' => true],
                    ['name' => 'label', 'type' => 'text', 'label' => 'Description', 'half' => true],
                    ['name' => 'image', 'type' => 'image', 'label' => 'Screenshot'],
                ]],
                $seo('results'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'international' => [
            'label' => 'International',
            'icon' => 'globe',
            'route' => 'international',
            'description' => 'Regions and countries on the 3D map and globe, routes and approach.',
            'sections' => [
                ['key' => 'pages.international.hero'] + $hero([
                    ['name' => 'scroll_hint', 'type' => 'text', 'label' => 'Scroll hint'],
                ]),
                ['key' => 'pages.international.story', 'title' => 'Globe story heading', 'icon' => 'globe', 'fields' => $heading(true)],
                ['key' => 'international.home', 'title' => 'Home base', 'icon' => 'pin', 'description' => 'Where every arc on the map and globe starts.', 'fields' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'City'],
                    ['name' => 'lat', 'type' => 'number', 'label' => 'Latitude', 'half' => true],
                    ['name' => 'lng', 'type' => 'number', 'label' => 'Longitude', 'half' => true],
                ]],
                ['key' => 'international.regions', 'type' => 'repeater', 'title' => 'Regions & countries', 'icon' => 'globe', 'description' => 'Each region is a chapter of the globe story. Latitude/longitude place the country on the map.', 'item_label' => 'name', 'fields' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Region name'],
                    ['name' => 'copy', 'type' => 'textarea', 'label' => 'Region description', 'rows' => 2],
                    ['name' => 'countries', 'type' => 'repeater', 'label' => 'Countries', 'item_label' => 'name', 'fields' => [
                        ['name' => 'name', 'type' => 'text', 'label' => 'Country', 'half' => true],
                        ['name' => 'code', 'type' => 'text', 'label' => 'Code', 'placeholder' => 'GB', 'half' => true],
                        ['name' => 'city', 'type' => 'text', 'label' => 'Reference city', 'half' => true],
                        ['name' => 'via', 'type' => 'text', 'label' => 'Worked with', 'half' => true],
                        ['name' => 'lat', 'type' => 'number', 'label' => 'Latitude', 'half' => true],
                        ['name' => 'lng', 'type' => 'number', 'label' => 'Longitude', 'half' => true],
                        ['name' => 'work', 'type' => 'textarea', 'label' => 'What was done there', 'rows' => 2],
                    ]],
                ]],
                ['key' => 'international.remote', 'type' => 'list', 'title' => 'Remote roles', 'icon' => 'briefcase', 'description' => 'Counted in the hero statistics.'],
                ['key' => 'pages.international.routes', 'title' => 'Routes board', 'icon' => 'send', 'description' => 'Distances are calculated automatically from the coordinates.', 'fields' => array_merge($heading(true), [
                    ['name' => 'from_code', 'type' => 'text', 'label' => 'Departure code', 'placeholder' => 'LHE'],
                ])],
                ['key' => 'pages.international.approach', 'title' => 'Approach heading', 'icon' => 'target', 'fields' => $heading()],
                ['key' => 'international.approach', 'type' => 'repeater', 'title' => 'Approach cards', 'icon' => 'target', 'item_label' => 'title', 'fields' => $iconCard],
                $seo('international'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'contact' => [
            'label' => 'Contact',
            'icon' => 'mail',
            'route' => 'contact',
            'description' => 'Contact details, social profiles and the message form.',
            'sections' => [
                ['key' => 'pages.contact.hero'] + $hero(),
                ['key' => 'contact', 'title' => 'Contact details', 'icon' => 'phone', 'description' => 'Used in the footer, contact page and floating menu.', 'fields' => [
                    ['name' => 'email', 'type' => 'email', 'label' => 'Email'],
                    ['name' => 'phone_display', 'type' => 'text', 'label' => 'Phone (as shown)', 'placeholder' => '+92 326 9083551', 'half' => true],
                    ['name' => 'phone_e164', 'type' => 'text', 'label' => 'Phone (for dialling)', 'placeholder' => '+923269083551', 'help' => 'No spaces.', 'half' => true],
                    ['name' => 'whatsapp', 'type' => 'url', 'label' => 'WhatsApp link', 'placeholder' => 'https://wa.me/92…'],
                ]],
                ['key' => 'social', 'type' => 'repeater', 'title' => 'Social profiles', 'icon' => 'linkedin', 'item_label' => 'name', 'fields' => [
                    ['name' => 'name', 'type' => 'text', 'label' => 'Network', 'half' => true],
                    ['name' => 'icon', 'type' => 'select', 'label' => 'Icon', 'options' => ['linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'whatsapp' => 'WhatsApp', 'mail' => 'Email', 'globe' => 'Website', 'play' => 'Video'], 'half' => true],
                    ['name' => 'url', 'type' => 'url', 'label' => 'Profile link'],
                    ['name' => 'handle', 'type' => 'text', 'label' => 'Handle'],
                ]],
                ['key' => 'pages.contact.labels', 'title' => 'Section labels', 'icon' => 'check', 'fields' => [
                    ['name' => 'social_title', 'type' => 'text', 'label' => 'Social profiles heading', 'half' => true],
                    ['name' => 'whatsapp_text', 'type' => 'text', 'label' => 'WhatsApp card text', 'half' => true],
                ]],
                ['key' => 'pages.contact.plane', 'title' => 'Paper-plane card', 'icon' => 'send', 'fields' => [
                    ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Label'],
                    ['name' => 'text', 'type' => 'text', 'label' => 'Text'],
                ]],
                ['key' => 'pages.contact.form', 'title' => 'Message form', 'icon' => 'mail', 'fields' => [
                    ['name' => 'title', 'type' => 'rich', 'label' => 'Heading', 'help' => $titleHelp, 'half' => true],
                    ['name' => 'subtitle', 'type' => 'text', 'label' => 'Subheading', 'half' => true],
                    ['name' => 'button', 'type' => 'text', 'label' => 'Button', 'half' => true],
                    ['name' => 'note', 'type' => 'text', 'label' => 'Privacy note', 'half' => true],
                    ['name' => 'success', 'type' => 'textarea', 'label' => 'Message after sending', 'rows' => 2],
                ]],
                $seo('contact'),
            ],
        ],

        /* ------------------------------------------------------------------ */
        'site' => [
            'label' => 'Footer & site-wide',
            'icon' => 'layers',
            'route' => null,
            'description' => 'The call-to-action panel and footer shown on every page.',
            'sections' => [
                ['key' => 'pages.footer.cta', 'title' => 'Call-to-action panel', 'icon' => 'sparkles', 'description' => 'The large panel above the footer (hidden on the Contact page).', 'fields' => [
                    ['name' => 'eyebrow', 'type' => 'text', 'label' => 'Badge'],
                    ['name' => 'title', 'type' => 'rich', 'label' => 'Heading', 'help' => $titleHelp],
                    ['name' => 'lead', 'type' => 'textarea', 'label' => 'Text', 'rows' => 3],
                    ['name' => 'primary_label', 'type' => 'text', 'label' => 'Main button', 'half' => true],
                    ['name' => 'secondary_label', 'type' => 'text', 'label' => 'WhatsApp button', 'half' => true],
                ]],
                ['key' => 'pages.footer.meta', 'title' => 'Footer text', 'icon' => 'layers', 'fields' => [
                    ['name' => 'tagline', 'type' => 'textarea', 'label' => 'Tagline', 'rows' => 3],
                    ['name' => 'copyright', 'type' => 'text', 'label' => 'Copyright line', 'help' => ':year is replaced by the current year.'],
                ]],
            ],
        ],
    ],
];
