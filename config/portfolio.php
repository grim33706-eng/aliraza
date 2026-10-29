<?php

/*
|--------------------------------------------------------------------------
| Portfolio content (defaults)
|--------------------------------------------------------------------------
|
| Every piece of copy, figure, link and image the site renders lives here,
| so the Blade views stay purely presentational. These are the *defaults*:
| sections edited in the admin panel are stored in the `contents` table and
| override the matching key at boot (see App\Support\ContentRepository).
|
| Source of truth for the defaults is Raza's résumé (PDF). Figures under the
| portfolio/results "evidence" are read directly from the Meta Ads Manager
| screenshots in public/images — nothing here is estimated.
|
| Text formatting in titles and leads: *word* = gradient accent, **word** = bold.
|
| Education: only the Bachelor's degree is listed, by design.
|
*/

return [

    'profile' => [
        'name' => 'Raza Sandhu',
        'title' => 'Social Media Manager & Performance Marketing Specialist',
        'short_title' => 'Social Media & Performance Marketing',
        'location' => 'Lahore, Punjab, Pakistan',
        'years' => '2+',
        'photo' => 'images/raza-photo.webp',
        'summary' => 'Social Media Manager and Performance Marketing Specialist with 2+ years of experience managing brand social accounts, building content calendars, publishing AI-assisted posts and videos, and running paid campaigns on Meta, Google and TikTok.',
        'intro' => [
            'I manage brand social accounts end to end: content strategy, monthly calendars, consistent posting and on-brand messaging across platforms. To keep the content pipeline steady I lean on AI tools — ChatGPT and Gemini for planning, copy and scripts; HeyGen and Captions AI for short-form video and reels.',
            'On the paid side I plan, build, launch and optimize campaigns on Meta Ads, Google Ads and TikTok Ads — from audience targeting and creative testing to budgets, bids and pixel setup. Every decision is anchored in the numbers: reach, impressions, engagement rate, CTR, CPC, CPL and ROAS.',
            'I\'ve run campaigns for businesses in Pakistan, Saudi Arabia, the UAE, Oman, Bangladesh, France and the UK, led a marketing team, and managed daily ad budgets of PKR 20,000–40,000 across client accounts.',
        ],
        'strengths' => [
            ['icon' => 'chart', 'title' => 'Data-led decisions', 'text' => 'Reach, CTR, CPC, CPL and ROAS guide every change to content plans and campaigns.'],
            ['icon' => 'flask', 'title' => 'Relentless testing', 'text' => 'A/B tests across audiences, creatives and copy — then scaling what actually converts.'],
            ['icon' => 'sparkles', 'title' => 'AI-assisted creation', 'text' => 'A steady content pipeline built with ChatGPT, Gemini, HeyGen and Captions AI.'],
            ['icon' => 'users', 'title' => 'Team & client leadership', 'text' => 'Led a marketing team and translated performance reports into client-ready recommendations.'],
        ],
    ],

    'contact' => [
        'email' => 'mr.razasandhu@gmail.com',
        'phone_display' => '+92 326 9083551',
        'phone_e164' => '+923269083551',
        // Carried over from the previous site; not listed on the résumé.
        'whatsapp' => 'https://wa.me/923194390885',
    ],

    // Not editable in the admin panel: where contact-form submissions are emailed.
    'mail' => [
        'inbox' => env('CONTACT_INBOX', 'mr.razasandhu@gmail.com'),
    ],

    'social' => [
        ['name' => 'LinkedIn', 'icon' => 'linkedin', 'url' => 'https://www.linkedin.com/in/mr-razasandhu', 'handle' => 'Razasandhu'],
        ['name' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://www.instagram.com/mr.razasandhu', 'handle' => '@mr.razasandhu'],
        ['name' => 'Facebook', 'icon' => 'facebook', 'url' => 'https://www.facebook.com/share/1E8pNiCStB/', 'handle' => 'Raza Sandhu'],
    ],

    'tools' => ['Meta Ads', 'Google Ads', 'TikTok Ads', 'ChatGPT', 'Gemini', 'HeyGen', 'Captions AI', 'Meta Pixel', 'TikTok Pixel'],

    'education' => [
        'degree' => 'Bachelor\'s in Information Technology',
        'institution' => 'Government College University Faisalabad (GCUF)',
        'location' => 'Faisalabad, Pakistan',
        'period' => '2021 – 2025',
    ],

    'experience' => [
        [
            'role' => 'Senior Performance Marketing Specialist',
            'company' => 'SuperWise International Pvt. Ltd.',
            'mode' => 'On-site',
            'period' => 'Aug 2026 – Present',
            'current' => true,
            'points' => [
                'Manage the company\'s social media accounts end to end — content strategy, monthly content calendars, regular posting and consistent brand messaging across platforms.',
                'Create social posts, short-form videos, reels, scripts and captions with ChatGPT, Gemini, HeyGen and Captions AI, using AI-assisted planning to keep a steady content pipeline.',
                'Plan, build, launch and optimize Meta Ads and Google Ads campaigns: setup, audience targeting, creatives, budgets and ongoing testing.',
                'Review platform insights and ad results — reach, impressions, engagement, CTR, CPC and lead metrics — to refine content, improve efficiency and support account growth.',
            ],
        ],
        [
            'role' => 'Performance Marketer',
            'company' => 'Chapter001',
            'mode' => 'Remote',
            'period' => 'Mar 2026 – Present',
            'current' => true,
            'points' => [
                'Plan social content calendars and regular posting, supporting brand visibility with AI-assisted post copy, video concepts, reels and engagement-focused creatives.',
                'Launch and optimize Meta and TikTok campaigns for education, cloud-restaurant and local-business clients — targeting, bids, budgets and creative testing.',
                'Monitor reach, engagement, traffic, CTR, CPL and ROAS, using performance reports to improve both organic content plans and paid campaign outcomes.',
            ],
        ],
        [
            'role' => 'Senior Performance Marketer',
            'company' => 'IU Affilboost',
            'mode' => 'On-site',
            'period' => 'Mar 2026 – Aug 2026',
            'current' => false,
            'points' => [
                'Managed Meta Ads and TikTok Ads for UK and France service businesses, generating qualified leads through tailored targeting, creative tests and conversion-focused campaigns.',
                'Developed social media content strategies and publishing plans; worked with creators on branded posts, reels and AI-assisted video content.',
                'Led the marketing team and managed daily ad budgets of PKR 20,000–40,000 across client accounts, testing audiences and creatives to optimize CPL.',
                'Analyzed ad performance, audience behavior and campaign KPIs; prepared reports and recommended changes aligned with business objectives.',
            ],
        ],
        [
            'role' => 'Assistant Performance Marketer',
            'company' => 'Digimarkyl',
            'mode' => 'Remote',
            'period' => 'Dec 2025 – Jul 2026',
            'current' => false,
            'points' => [
                'Supported company and client social media management through content calendars, platform posting, creative coordination and AI-assisted posts and videos.',
                'Helped execute and optimize Meta campaigns in Pakistan, Saudi Arabia, the UAE, Oman and Bangladesh through segmentation, budget monitoring and A/B tests.',
                'Tracked campaign and social KPIs, contributed performance reports and worked with creative teams to improve brand presence and lead generation.',
            ],
        ],
        [
            'role' => 'Performance Marketer — Paid Internship',
            'company' => 'Adymizing',
            'mode' => 'On-site',
            'period' => 'Sep 2025 – Dec 2025',
            'current' => false,
            'points' => [
                'Supported social content planning, scheduled publishing and the preparation of posts, video content and advertising creatives.',
                'Managed Meta campaigns for e-commerce, digital-services and education clients; tested cold, retargeting and lookalike audiences while monitoring ROAS, CTR, CPL and CPA.',
            ],
        ],
        [
            'role' => 'Performance Marketer — Internship',
            'company' => 'Nextech Skills',
            'mode' => 'On-site',
            'period' => 'May 2025 – Aug 2025',
            'current' => false,
            'points' => [
                'Assisted with content calendars, social posting and brand-awareness creatives, including AI-supported posts and short-form video concepts.',
                'Supported Meta Ads setup, student lead-generation funnels, audience targeting and A/B testing, tracking CTR, CPM, CPL and conversions.',
            ],
        ],
    ],

    'skills' => [
        [
            'name' => 'Social & Content',
            'skills' => [
                'Social Media Management & Posting',
                'Content Calendars & Scheduling',
                'Organic Growth & Community Engagement',
                'AI Content: ChatGPT & Gemini',
                'AI Videos & Reels: HeyGen, Captions AI',
                'Content Performance & Audience Insights',
            ],
        ],
        [
            'name' => 'Paid Media',
            'skills' => [
                'Meta Ads & Meta Pixel Setup',
                'Google Ads (Search, Display, PMax, Remarketing)',
                'TikTok Ads & TikTok Pixel Setup',
                'Keyword Research & Bidding Strategy',
                'Lead Generation & Paid Campaigns',
                'Audience Targeting & Segmentation',
            ],
        ],
        [
            'name' => 'Strategy & Analytics',
            'skills' => [
                'A/B Testing & Creative Optimization',
                'Ad Copywriting & Creative Strategy',
                'Campaign Reporting & Performance Analysis',
                'Reach, Impressions & Engagement Analysis',
                'KPIs: CTR, CPC, CPM, CPL, CPA & ROAS',
                'Budget Management & Cost Optimization',
                'Multi-Platform Brand Growth Strategy',
                'Team Leadership & Client Communication',
            ],
        ],
    ],

    // Service groups (shown as sections on the Services page, in this order).
    'service_groups' => [
        ['name' => 'Social & Content', 'title' => 'Social & *content*', 'text' => 'Keep your channels active, on-brand and growing — with an AI-assisted pipeline that never runs dry.'],
        ['name' => 'Paid Media', 'title' => 'Paid *media*', 'text' => 'Campaigns on Meta, Google and TikTok, set up properly and optimized for cost per lead.'],
        ['name' => 'Strategy & Analytics', 'title' => 'Strategy & *analytics*', 'text' => 'Testing and reporting that turn platform data into clear next steps.'],
    ],

    'services' => [
        [
            'slug' => 'social-media-management',
            'group' => 'Social & Content',
            'icon' => 'megaphone',
            'title' => 'Social Media Management',
            'text' => 'End-to-end management of your brand\'s social accounts — strategy, posting and consistent messaging across every platform.',
            'benefits' => ['Content strategy aligned with business goals', 'Regular, on-brand posting', 'Community engagement & organic growth'],
        ],
        [
            'slug' => 'content-calendars',
            'group' => 'Social & Content',
            'icon' => 'calendar',
            'title' => 'Content Calendars & Scheduling',
            'text' => 'Monthly content calendars and scheduled publishing, so your channels never go quiet and every post has a purpose.',
            'benefits' => ['Monthly planning & approvals', 'Scheduled multi-platform publishing', 'A steady, predictable pipeline'],
        ],
        [
            'slug' => 'ai-content',
            'group' => 'Social & Content',
            'icon' => 'sparkles',
            'title' => 'AI-Assisted Content & Video',
            'text' => 'Posts, short-form videos, reels, scripts and captions produced with ChatGPT, Gemini, HeyGen and Captions AI.',
            'benefits' => ['Scripts, captions & post copy', 'AI video & reels production', 'Faster output without losing your voice'],
        ],
        [
            'slug' => 'meta-ads',
            'group' => 'Paid Media',
            'icon' => 'target',
            'title' => 'Meta Ads (Facebook & Instagram)',
            'text' => 'Campaign setup, audience targeting, creatives, budgets and continuous testing — backed by a correctly configured Meta Pixel.',
            'benefits' => ['Cold, retargeting & lookalike audiences', 'Lead, messaging & engagement objectives', 'Meta Pixel setup & tracking'],
        ],
        [
            'slug' => 'google-ads',
            'group' => 'Paid Media',
            'icon' => 'search',
            'title' => 'Google Ads',
            'text' => 'Search, Display, Performance Max and Remarketing campaigns built on solid keyword research and the right bidding strategy.',
            'benefits' => ['Keyword research & bidding strategy', 'Search, Display, PMax & Remarketing', 'Ongoing optimization for lead cost'],
        ],
        [
            'slug' => 'tiktok-ads',
            'group' => 'Paid Media',
            'icon' => 'play',
            'title' => 'TikTok Ads',
            'text' => 'TikTok campaigns with native-feeling creative, tight targeting and TikTok Pixel tracking for measurable results.',
            'benefits' => ['TikTok Pixel setup', 'Creative tests built for the platform', 'Budget & bid management'],
        ],
        [
            'slug' => 'lead-generation',
            'group' => 'Paid Media',
            'icon' => 'funnel',
            'title' => 'Lead Generation Campaigns',
            'text' => 'Conversion-focused funnels — instant forms, messaging and landing-page campaigns — optimized to lower cost per lead.',
            'benefits' => ['Qualified-lead focus, not just volume', 'CPL-driven audience & creative tests', 'Daily budget monitoring'],
        ],
        [
            'slug' => 'ab-testing',
            'group' => 'Strategy & Analytics',
            'icon' => 'flask',
            'title' => 'A/B Testing & Creative Strategy',
            'text' => 'Structured tests across hooks, formats, ad copy and audiences, so budget follows the creative that performs.',
            'benefits' => ['Ad copywriting & creative direction', 'Audience & placement tests', 'Clear winners, scaled quickly'],
        ],
        [
            'slug' => 'reporting',
            'group' => 'Strategy & Analytics',
            'icon' => 'chart',
            'title' => 'Reporting & Performance Analysis',
            'text' => 'Plain-language reports on reach, impressions, engagement, CTR, CPC, CPM, CPL, CPA and ROAS — with recommended next steps.',
            'benefits' => ['KPI dashboards & regular reports', 'Content & audience insights', 'Recommendations tied to business goals'],
        ],
    ],

    'process' => [
        ['title' => 'Plan', 'text' => 'Goals, audiences, content calendar and campaign structure agreed up front.'],
        ['title' => 'Create', 'text' => 'Posts, reels, scripts and ad creatives produced with AI-assisted workflows.'],
        ['title' => 'Launch', 'text' => 'Campaigns and pixels set up correctly, budgets and bids in place.'],
        ['title' => 'Optimize', 'text' => 'Daily monitoring, A/B tests and reports that turn data into next steps.'],
    ],

    /*
    | Portfolio. Items with media_type "image"/"video" are real Ads Manager
    | screenshots and screen recordings ("image" is the screenshot, or the
    | poster frame for a video); "none" items describe work from the résumé
    | and show an icon instead. Filters refer to keys in portfolio_filters.
    */
    'portfolio' => [
        [
            'slug' => 'natu-furniture-uk',
            'title' => 'Instant-form lead generation at scale',
            'client' => 'Natu Furniture UK',
            'market' => 'United Kingdom',
            'media_type' => 'image',
            'image' => 'images/result-dashboard-1.webp',
            'video' => '',
            'width' => 1600,
            'height' => 720,
            'icon' => 'funnel',
            'filters' => ['evidence', 'lead-gen', 'paid'],
            'text' => 'Seventeen concurrent instant-form lead campaigns, each run for an individual sales rep and optimized against its own cost per lead.',
            'stats' => ['17 active lead campaigns', '1,008 form leads (visible rows)', 'Lowest CPL Rs 252.23'],
            'tags' => ['Meta Ads', 'Instant Forms', 'Highest-volume bidding'],
        ],
        [
            'slug' => 'nine-campaign-account',
            'title' => '9-campaign engagement & messaging account',
            'client' => 'Service business · Lahore',
            'market' => 'Pakistan',
            'media_type' => 'image',
            'image' => 'images/result-dashboard-2.webp',
            'video' => '',
            'width' => 1600,
            'height' => 412,
            'icon' => 'chart',
            'filters' => ['evidence', 'social', 'paid'],
            'text' => 'A mix of page-like, WhatsApp-messaging, seasonal engagement, lead and awareness campaigns, tuned for cost per result.',
            'stats' => ['235,122 reach', '1.44% CTR', '649 messaging conversations'],
            'tags' => ['Meta Ads', 'WhatsApp messaging', 'Awareness'],
        ],
        [
            'slug' => 'daily-monitoring',
            'title' => 'Daily campaign monitoring',
            'client' => 'Screen recording',
            'market' => '',
            'media_type' => 'video',
            'image' => 'images/result-video-1-poster.webp',
            'video' => 'videos/result-video-1.mp4',
            'width' => 800,
            'height' => 451,
            'icon' => 'play',
            'filters' => ['evidence', 'paid'],
            'text' => 'Walking through active campaigns in Ads Manager, checking cost per result and delivery status account-wide.',
            'stats' => [],
            'tags' => ['Ads Manager', 'Monitoring'],
        ],
        [
            'slug' => 'budget-reallocation',
            'title' => 'Budget review & reallocation',
            'client' => 'Screen recording',
            'market' => '',
            'media_type' => 'video',
            'image' => 'images/result-video-2-poster.webp',
            'video' => 'videos/result-video-2.mp4',
            'width' => 800,
            'height' => 451,
            'icon' => 'play',
            'filters' => ['evidence', 'paid'],
            'text' => 'Comparing ad-set performance to move daily budget toward the campaigns delivering the lowest cost per result.',
            'stats' => [],
            'tags' => ['Budget management', 'Optimization'],
        ],
        [
            'slug' => 'client-reporting',
            'title' => 'Client reporting walkthrough',
            'client' => 'Screen recording',
            'market' => '',
            'media_type' => 'video',
            'image' => 'images/result-video-3-poster.webp',
            'video' => 'videos/result-video-3.mp4',
            'width' => 800,
            'height' => 453,
            'icon' => 'play',
            'filters' => ['evidence', 'paid'],
            'text' => 'Breaking account results — reach, impressions and CTR — down into a plain-language update for the client.',
            'stats' => [],
            'tags' => ['Reporting', 'Client communication'],
        ],
        [
            'slug' => 'superwise-social-paid',
            'title' => 'Brand social + Meta & Google Ads programme',
            'client' => 'SuperWise International',
            'market' => 'Pakistan',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'megaphone',
            'filters' => ['social', 'paid'],
            'text' => 'Company social accounts run end to end alongside Meta and Google Ads — monthly calendars, AI-produced reels and videos, and campaigns refined from platform insights.',
            'stats' => [],
            'tags' => ['Meta Ads', 'Google Ads', 'HeyGen', 'Captions AI'],
        ],
        [
            'slug' => 'uk-france-lead-gen',
            'title' => 'UK & France service-business lead generation',
            'client' => 'IU Affilboost',
            'market' => 'UK · France',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'funnel',
            'filters' => ['lead-gen', 'paid'],
            'text' => 'Meta and TikTok campaigns built around qualified leads, with creative tests and daily budgets of PKR 20,000–40,000 across client accounts.',
            'stats' => [],
            'tags' => ['Meta Ads', 'TikTok Ads', 'CPL optimization'],
        ],
        [
            'slug' => 'multi-sector-campaigns',
            'title' => 'Education, cloud-restaurant & local-business campaigns',
            'client' => 'Chapter001',
            'market' => 'Remote',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'layers',
            'filters' => ['social', 'paid'],
            'text' => 'Social content calendars paired with Meta and TikTok campaigns, tracked on reach, traffic, CTR, CPL and ROAS.',
            'stats' => [],
            'tags' => ['Meta Ads', 'TikTok Ads', 'Content calendars'],
        ],
        [
            'slug' => 'gcc-south-asia',
            'title' => 'Meta campaigns across five markets',
            'client' => 'Digimarkyl',
            'market' => 'PK · KSA · UAE · Oman · BD',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'globe',
            'filters' => ['lead-gen', 'paid'],
            'text' => 'Segmentation, budget monitoring and A/B tests for Meta campaigns in Pakistan, Saudi Arabia, the UAE, Oman and Bangladesh.',
            'stats' => [],
            'tags' => ['Meta Ads', 'Segmentation', 'A/B testing'],
        ],
        [
            'slug' => 'full-funnel-meta',
            'title' => 'Full-funnel Meta for e-commerce & education',
            'client' => 'Adymizing',
            'market' => '',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'target',
            'filters' => ['paid'],
            'text' => 'Cold, retargeting and lookalike audiences tested for e-commerce, digital-services and education clients, monitored on ROAS, CTR, CPL and CPA.',
            'stats' => [],
            'tags' => ['Meta Ads', 'Lookalikes', 'Retargeting'],
        ],
        [
            'slug' => 'student-lead-funnels',
            'title' => 'Student lead-generation funnels',
            'client' => 'Nextech Skills',
            'market' => '',
            'media_type' => 'none',
            'image' => '',
            'video' => '',
            'width' => null,
            'height' => null,
            'icon' => 'graduation',
            'filters' => ['lead-gen', 'paid'],
            'text' => 'Meta Ads setup, audience targeting and A/B tests for student lead-generation funnels, tracked on CTR, CPM, CPL and conversions.',
            'stats' => [],
            'tags' => ['Meta Ads', 'Lead forms', 'Conversion tracking'],
        ],
    ],

    // Filter buttons on the Portfolio page ("all" always shows everything).
    'portfolio_filters' => [
        ['key' => 'all', 'label' => 'All work'],
        ['key' => 'evidence', 'label' => 'Ads Manager proof'],
        ['key' => 'lead-gen', 'label' => 'Lead generation'],
        ['key' => 'social', 'label' => 'Social & content'],
        ['key' => 'paid', 'label' => 'Paid campaigns'],
    ],

    /*
    | Results — "source" on each highlight refers to a key in results.sources.
    */
    'results' => [
        'highlights' => [
            ['value' => 299081, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'Impressions', 'source' => 'B'],
            ['value' => 235122, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'People reached', 'source' => 'B'],
            ['value' => 1008, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'Form leads (visible campaigns)', 'source' => 'A'],
            ['value' => 649, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'Messaging conversations', 'source' => 'B'],
            ['value' => 1.44, 'decimals' => 2, 'prefix' => '', 'suffix' => '%', 'label' => 'Account CTR (all)', 'source' => 'B'],
            ['value' => 5.38, 'decimals' => 2, 'prefix' => 'Rs ', 'suffix' => '', 'label' => 'Cost per click', 'source' => 'B'],
            ['value' => 252.23, 'decimals' => 2, 'prefix' => 'Rs ', 'suffix' => '', 'label' => 'Lowest cost per lead', 'source' => 'A'],
            ['value' => 17, 'decimals' => 0, 'prefix' => '', 'suffix' => '', 'label' => 'Concurrent lead campaigns', 'source' => 'A'],
        ],
        'career' => [
            ['value' => 2, 'suffix' => '+', 'label' => 'Years of experience'],
            ['value' => 6, 'suffix' => '', 'label' => 'Marketing roles'],
            ['value' => 7, 'suffix' => '', 'label' => 'Countries served'],
            ['value' => 3, 'suffix' => '', 'label' => 'Ad platforms'],
        ],
        'sources' => [
            ['key' => 'A', 'label' => 'Natu Furniture UK lead account', 'image' => 'images/result-dashboard-1.webp'],
            ['key' => 'B', 'label' => '9-campaign engagement & messaging account', 'image' => 'images/result-dashboard-2.webp'],
        ],
        // Leads per campaign, visible rows of screenshot A (campaigns with a result).
        'leads_chart' => [4, 7, 3, 9, 17, 51, 13, 34, 109, 98, 115, 100, 75, 94, 177, 102],
        // CTR (all) per campaign, screenshot B.
        'ctr_chart' => [
            ['label' => 'Page likes', 'value' => 15.46],
            ['label' => 'Diet-plan leads', 'value' => 4.63],
            ['label' => 'Engagement messages (May)', 'value' => 4.21],
            ['label' => 'New-deals leads, Lahore', 'value' => 3.38],
            ['label' => 'Mother\'s Day messages', 'value' => 2.79],
            ['label' => 'Engagement messages', 'value' => 2.21],
            ['label' => 'Father\'s Day messages', 'value' => 1.83],
            ['label' => 'WhatsApp video engagement', 'value' => 1.45],
            ['label' => 'Awareness, Lahore', 'value' => 0.51],
        ],
        'achievements' => [
            ['icon' => 'users', 'title' => 'Led a marketing team', 'text' => 'Led the marketing team at IU Affilboost while managing client ad accounts.'],
            ['icon' => 'wallet', 'title' => 'PKR 20,000–40,000 daily budgets', 'text' => 'Managed daily ad budgets across client accounts, testing audiences and creatives to optimize CPL.'],
            ['icon' => 'globe', 'title' => '7 countries, 3 regions', 'text' => 'Campaigns for businesses in South Asia, the Gulf and Europe.'],
            ['icon' => 'trending', 'title' => 'Intern to senior in 15 months', 'text' => 'From a Meta Ads internship in May 2025 to a Senior Performance Marketing Specialist role in August 2026.'],
        ],
    ],

    /*
    | International — lat/lng mark one reference city per country (the
    | capital, or Dubai for the UAE). Regions show in this order.
    */
    'international' => [
        'home' => ['name' => 'Lahore', 'lat' => 31.55, 'lng' => 74.34],
        'regions' => [
            [
                'name' => 'Europe',
                'copy' => 'Lead generation for UK and France service businesses — tailored targeting, creative tests and conversion-focused campaigns.',
                'countries' => [
                    ['code' => 'GB', 'name' => 'United Kingdom', 'city' => 'London', 'lat' => 51.51, 'lng' => -0.13, 'via' => 'IU Affilboost', 'work' => 'Meta & TikTok lead generation for service businesses, plus the Natu Furniture UK instant-form account featured in the portfolio.'],
                    ['code' => 'FR', 'name' => 'France', 'city' => 'Paris', 'lat' => 48.86, 'lng' => 2.35, 'via' => 'IU Affilboost', 'work' => 'Meta & TikTok lead generation for service businesses.'],
                ],
            ],
            [
                'name' => 'Gulf',
                'copy' => 'Meta campaigns in Saudi Arabia, the UAE and Oman, run with audience segmentation, budget monitoring and A/B tests.',
                'countries' => [
                    ['code' => 'SA', 'name' => 'Saudi Arabia', 'city' => 'Riyadh', 'lat' => 24.71, 'lng' => 46.68, 'via' => 'Digimarkyl', 'work' => 'Meta campaigns with audience segmentation, budget monitoring and A/B tests.'],
                    ['code' => 'AE', 'name' => 'United Arab Emirates', 'city' => 'Dubai', 'lat' => 25.20, 'lng' => 55.27, 'via' => 'Digimarkyl', 'work' => 'Meta campaigns with audience segmentation, budget monitoring and A/B tests.'],
                    ['code' => 'OM', 'name' => 'Oman', 'city' => 'Muscat', 'lat' => 23.59, 'lng' => 58.41, 'via' => 'Digimarkyl', 'work' => 'Meta campaigns with audience segmentation, budget monitoring and A/B tests.'],
                ],
            ],
            [
                'name' => 'South Asia',
                'copy' => 'Home base. Social media management and Meta, Google and TikTok campaigns in Pakistan, plus Meta campaigns in Bangladesh.',
                'countries' => [
                    ['code' => 'PK', 'name' => 'Pakistan', 'city' => 'Lahore', 'lat' => 31.55, 'lng' => 74.34, 'via' => 'Home market', 'work' => 'Social media management and Meta, Google & TikTok campaigns — the base for every role.'],
                    ['code' => 'BD', 'name' => 'Bangladesh', 'city' => 'Dhaka', 'lat' => 23.81, 'lng' => 90.41, 'via' => 'Digimarkyl', 'work' => 'Meta campaigns with audience segmentation, budget monitoring and A/B tests.'],
                ],
            ],
        ],
        'remote' => ['Chapter001', 'Digimarkyl'],
        'approach' => [
            ['icon' => 'target', 'title' => 'Market-by-market segmentation', 'text' => 'Audiences built per country rather than one broad regional set, so targeting matches each market.'],
            ['icon' => 'wallet', 'title' => 'Budget monitoring', 'text' => 'Spend tracked per market and moved toward the campaigns delivering the lowest cost per lead.'],
            ['icon' => 'flask', 'title' => 'A/B tests in every market', 'text' => 'Creatives and audiences tested locally — what wins in Lahore may not win in London.'],
            ['icon' => 'globe', 'title' => 'Remote collaboration', 'text' => 'Remote roles with Chapter001 and Digimarkyl, working with creative teams and clients across time zones.'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page copy — headings, leads, buttons and labels for every section.
    |--------------------------------------------------------------------------
    */
    'pages' => [

        'home' => [
            'seo' => ['title' => '', 'description' => ''],
            'hero' => [
                'eyebrow' => 'Available for new projects · Lahore, Pakistan',
                'title' => 'Content that builds brands. Campaigns that bring *leads.*',
                'lead' => 'I\'m **Raza Sandhu**, a Social Media Manager and Performance Marketing Specialist. I run brand social accounts and paid campaigns on Meta, Google and TikTok for businesses in seven countries.',
                'primary_label' => 'View my work',
                'secondary_label' => 'Let\'s talk',
                'platforms' => [
                    ['icon' => 'facebook', 'label' => 'Meta Ads'],
                    ['icon' => 'search', 'label' => 'Google Ads'],
                    ['icon' => 'play', 'label' => 'TikTok Ads'],
                ],
                'kpis' => [
                    ['icon' => 'users', 'label' => 'People reached', 'value' => 235122, 'decimals' => 0, 'prefix' => '', 'suffix' => ''],
                    ['icon' => 'funnel', 'label' => 'Form leads', 'value' => 1008, 'decimals' => 0, 'prefix' => '', 'suffix' => ''],
                    ['icon' => 'trending', 'label' => 'Account CTR', 'value' => 1.44, 'decimals' => 2, 'prefix' => '', 'suffix' => '%'],
                ],
                'kpi_note' => 'From live Meta Ads Manager accounts',
                'meta_strong' => '2+',
                'meta_text' => 'years · 6 roles · 7 countries',
            ],
            'marquee' => [
                'second_row' => ['Content calendars', 'Reels & short-form video', 'Lead generation', 'A/B testing', 'Audience targeting', 'Pixel setup', 'Performance reporting', 'Budget optimization'],
            ],
            'about' => [
                'eyebrow' => '01 · About',
                'title' => 'Social media and paid performance, handled by one person who *reads the numbers.*',
                'link_label' => 'More about my journey',
                'badge_text' => 'years in social & performance marketing',
                'chips' => [
                    ['icon' => 'target', 'label' => 'Meta Ads'],
                    ['icon' => 'sparkles', 'label' => 'AI content'],
                    ['icon' => 'chart', 'label' => 'Reporting'],
                ],
            ],
            'services' => [
                'eyebrow' => '02 · Services',
                'title' => 'What I can take *off your plate*',
                'link_label' => 'All :count services',
                'cta_text' => 'services across social, paid media and analytics',
                'cta_button' => 'Explore all',
            ],
            'portfolio' => [
                'eyebrow' => '03 · Portfolio',
                'title' => 'Straight from the *Ads Manager*',
                'link_label' => 'See the full portfolio',
            ],
            'results' => [
                'eyebrow' => '04 · Results',
                'title' => 'Numbers you can *check*',
                'link_label' => 'Explore the results',
            ],
            'international' => [
                'eyebrow' => '05 · International',
                'title' => 'Campaigns across *three regions*',
                'lead' => 'From Lahore to London — Meta, TikTok and Google campaigns for businesses in South Asia, the Gulf and Europe.',
                'link_label' => 'See the international work',
            ],
        ],

        'about' => [
            'seo' => ['title' => 'About', 'description' => 'About Raza Sandhu — Social Media Manager and Performance Marketing Specialist in Lahore. Career journey, expertise, skills and education.'],
            'hero' => [
                'eyebrow' => 'About',
                'title' => 'The person behind the content calendar *and the ad account.*',
                'lead' => 'Two-plus years, six roles and one consistent habit: plan the content, launch the campaign, read the numbers, improve — then do it again.',
                'watermark' => 'Journey',
            ],
            'intro' => [
                'eyebrow' => 'Introduction',
                'title' => 'Hi, I\'m *Raza.*',
                'primary_label' => 'Work with me',
                'secondary_label' => 'LinkedIn',
                'facts' => [
                    ['icon' => 'pin', 'label' => 'Based in', 'value' => 'Lahore, Pakistan'],
                    ['icon' => 'clock', 'label' => 'Experience', 'value' => '2+ years'],
                    ['icon' => 'briefcase', 'label' => 'Roles', 'value' => '6 positions'],
                    ['icon' => 'graduation', 'label' => 'Degree', 'value' => 'BS IT, GCUF'],
                ],
            ],
            'strengths' => ['eyebrow' => 'How I work', 'title' => 'Strengths I bring to *every account*'],
            'experience' => [
                'eyebrow' => 'Career journey',
                'title' => 'Experience',
                'lead' => 'From a Meta Ads internship in 2025 to a senior role managing a company\'s social channels and paid media.',
            ],
            'skills' => [
                'eyebrow' => 'Expertise',
                'title' => 'Skills & *tools*',
                'lead' => 'Twenty skills across organic social, paid media and analytics. Drag the sphere to spin it.',
                'hint' => 'Drag to spin',
            ],
            'education' => [
                'eyebrow' => 'Education',
                'title' => 'Academic *background*',
                'label' => 'Bachelor\'s degree',
                'seal_text' => 'INFORMATION TECHNOLOGY • GCUF',
                'seal_small' => 'BS',
                'seal_big' => 'IT',
            ],
        ],

        'services' => [
            'seo' => ['title' => 'Services', 'description' => 'Social media management, content calendars, AI-assisted content, Meta Ads, Google Ads, TikTok Ads, lead generation, A/B testing and reporting by Raza Sandhu.'],
            'hero' => [
                'eyebrow' => 'Services',
                'title' => 'Everything your brand needs to grow on social — *and to get paid for it.*',
                'lead' => 'Nine services across organic social, paid media and analytics — pick one, or let me run the whole engine from content calendar to cost-per-lead report.',
                'watermark' => 'Services',
                'primary_label' => 'Explore services',
                'secondary_label' => 'Get a proposal',
            ],
            'cards' => ['cta_label' => 'Discuss this service', 'count_label' => 'services'],
            'process' => [
                'eyebrow' => 'Process',
                'title' => 'How a project *runs*',
                'lead' => 'The same four-step loop powers organic content and paid campaigns alike.',
            ],
            'toolkit' => [
                'eyebrow' => 'Toolkit',
                'title' => 'Platforms & tools *in orbit*',
                'lead' => 'The ad platforms, AI tools and tracking pixels behind every campaign and content calendar.',
            ],
        ],

        'portfolio' => [
            'seo' => ['title' => 'Portfolio', 'description' => 'Portfolio of Raza Sandhu: real Meta Ads Manager dashboards, screen recordings and social media and paid campaign work across lead generation, content and paid media.'],
            'hero' => [
                'eyebrow' => 'Portfolio',
                'title' => 'Work samples, *straight from the accounts.*',
                'lead' => 'Real Ads Manager dashboards and screen recordings, alongside the social and paid campaign work behind each role. No mock-ups.',
                'watermark' => 'Work',
            ],
            'gallery' => [
                'eyebrow' => 'Evidence wall',
                'title' => ':count captures, *one scroll.*',
                'hint' => 'Keep scrolling to sweep through the wall',
            ],
            'grid' => [
                'eyebrow' => 'All work',
                'title' => 'Filter by *what you need*',
                'footnote' => 'Screenshots and recordings are unedited captures from Meta Ads Manager. Figures quoted on each card are read directly from the visible rows of the screenshot.',
            ],
        ],

        'results' => [
            'seo' => ['title' => 'Results', 'description' => 'Measured results from Raza Sandhu\'s Meta Ads campaigns — reach, impressions, CTR, CPC, form leads and messaging conversations, read directly from Ads Manager.'],
            'hero' => [
                'eyebrow' => 'Results & achievements',
                'title' => 'Results measured in reach, *clicks and leads.*',
                'lead' => 'Every figure on this page is read directly from a live Meta Ads Manager screenshot or stated on my résumé. Sources are linked at the bottom.',
                'watermark' => 'Results',
            ],
            'highlights' => ['eyebrow' => 'By the numbers', 'title' => 'Headline figures from *two live accounts*'],
            'charts' => [
                'eyebrow' => 'Charts',
                'title' => 'The data, *visualised*',
                'leads_eyebrow' => 'Source A · Lead generation',
                'leads_title' => 'Form leads per campaign',
                'leads_sub' => ':count instant-form campaigns with recorded leads · :total leads in total',
                'ctr_eyebrow' => 'Source B · Engagement & messaging',
                'ctr_title' => 'Click-through rate by campaign, in 3D',
                'ctr_sub' => ':count campaigns · 1.44% CTR account-wide. Hover a campaign to find its bar.',
            ],
            'achievements' => ['eyebrow' => 'Achievements', 'title' => 'Career *highlights*'],
            'milestones' => [
                'eyebrow' => 'Milestones',
                'title' => 'Six roles in *fifteen months*',
                'lead' => 'Each role added responsibility — from supporting campaigns to leading a team and owning a brand\'s social and paid channels.',
            ],
            'sources' => [
                'eyebrow' => 'Sources',
                'title' => 'Where these numbers *come from*',
                'footnote' => 'Both screenshots use Ads Manager\'s maximum (lifetime) date range ending 29 July 2026. Campaign names in the charts are shortened or numbered to keep individual sales reps anonymous. Career figures come from the résumé.',
            ],
        ],

        'international' => [
            'seo' => ['title' => 'International Experience', 'description' => 'Raza Sandhu\'s international marketing experience: Meta, TikTok and Google campaigns for businesses in Pakistan, Saudi Arabia, the UAE, Oman, Bangladesh, France and the United Kingdom.'],
            'hero' => [
                'eyebrow' => 'International experience',
                'title' => 'One desk in Lahore. *Campaigns in seven countries.*',
                'lead' => 'Meta and TikTok campaigns for businesses across South Asia, the Gulf and Europe — each with its own audiences, budgets and creative tests.',
                'watermark' => 'Worldwide',
                'scroll_hint' => 'Scroll to fly around the globe',
            ],
            'story' => [
                'eyebrow' => 'Markets',
                'title' => 'Where the *campaigns ran*',
                'lead' => 'Scroll through the regions — the globe turns to follow. Hover or tap a country to find it.',
            ],
            'routes' => [
                'eyebrow' => 'Routes',
                'title' => 'Lahore to *every market*',
                'lead' => 'Straight-line distance from Lahore to a reference city in each market — the reach of one remote-friendly desk.',
                'from_code' => 'LHE',
            ],
            'approach' => ['eyebrow' => 'Approach', 'title' => 'Running campaigns *across borders*'],
        ],

        'contact' => [
            'seo' => ['title' => 'Contact', 'description' => 'Contact Raza Sandhu about social media management, Meta Ads, Google Ads, TikTok Ads or lead generation. Based in Lahore, Pakistan.'],
            'hero' => [
                'eyebrow' => 'Contact',
                'title' => 'Let\'s grow something *together.*',
                'lead' => 'Tell me about your brand and what you\'d like social or paid campaigns to achieve — leads, bookings, sign-ups or awareness. I reply to every message personally.',
                'watermark' => 'Hello',
            ],
            'plane' => ['eyebrow' => 'Direct to inbox', 'text' => 'Messages from the form are emailed straight to Raza.'],
            'labels' => [
                'social_title' => 'Find me online',
                'whatsapp_text' => 'Send a message',
            ],
            'form' => [
                'title' => 'Send a *message*',
                'subtitle' => 'All fields are required.',
                'button' => 'Send message',
                'note' => 'Your details are only used to reply to you.',
                'success' => 'Thanks — your message is on its way. I\'ll reply to you by email shortly.',
            ],
        ],

        'footer' => [
            'cta' => [
                'eyebrow' => 'Available for new projects',
                'title' => 'Have a brand to grow or a *campaign to fix?*',
                'lead' => 'Tell me where you want to be — more leads, lower cost per lead, or a social presence that actually posts. I\'ll tell you how I\'d get there.',
                'primary_label' => 'Start a conversation',
                'secondary_label' => 'WhatsApp',
            ],
            'meta' => [
                'tagline' => 'Social Media Manager & Performance Marketing Specialist. Based in Lahore, Punjab, Pakistan, working with brands across South Asia, the Gulf and Europe.',
                'copyright' => '© :year Raza Sandhu. All rights reserved.',
            ],
        ],
    ],

];
