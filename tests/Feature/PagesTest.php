<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PagesTest extends TestCase
{
    public static function pages(): array
    {
        return [
            'home' => ['/', 'Content that builds brands'],
            'about' => ['/about', 'Experience'],
            'services' => ['/services', 'Meta Ads (Facebook &amp; Instagram)'],
            'portfolio' => ['/portfolio', 'Natu Furniture UK'],
            'results' => ['/results', '235,122'],
            'international' => ['/international', 'Saudi Arabia'],
            'contact' => ['/contact', 'Send a message'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_renders(string $uri, string $expected): void
    {
        $this->get($uri)
            ->assertOk()
            ->assertSee($expected, false)
            ->assertSee('<title>', false)
            ->assertSee('name="description"', false);
    }

    #[DataProvider('pages')]
    public function test_only_the_bachelors_degree_is_mentioned(string $uri, string $expected): void
    {
        $html = $this->get($uri)->getContent();

        foreach (['FSC', 'F.Sc', 'Matric', 'Intermediate', 'SSC', 'HSSC'] as $term) {
            $this->assertDoesNotMatchRegularExpression('/\b'.preg_quote($term, '/').'\b/i', strip_tags($html), "\"{$term}\" appears on {$uri}");
        }
    }

    public function test_about_page_lists_education_and_every_role(): void
    {
        $response = $this->get('/about')->assertSee('Bachelor&#039;s in Information Technology', false);

        foreach (config('portfolio.experience') as $job) {
            $response->assertSee(e($job['company']), false);
        }
    }

    public function test_navigation_marks_the_active_page(): void
    {
        $this->assertMatchesRegularExpression('/href="[^"]*\/services" class="nav-link is-active"\s+aria-current="page"/', $this->get('/services')->getContent());
    }

    public function test_service_links_prefill_the_contact_subject(): void
    {
        $this->get('/contact?subject=Google+Ads')->assertSee('value="Google Ads"', false);
    }

    public function test_sitemap_lists_all_pages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('international'), false);
    }

    public function test_unknown_page_shows_custom_404(): void
    {
        $this->get('/nope')->assertNotFound()->assertSee("This page didn't convert.", false);
    }

    public function test_home_has_structured_data(): void
    {
        $this->get('/')->assertSee('"@context":"https://schema.org"', false);
    }
}
