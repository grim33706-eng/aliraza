<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'services' => array_slice(config('portfolio.services'), 0, 4),
            'featured' => collect(config('portfolio.portfolio'))->where('media_type', '!=', 'none')->take(2)->values(),
            'markets' => $this->markets(),
            'schema' => $this->personSchema(),
        ]);
    }

    /**
     * schema.org Person data for search engines. Built here rather than in
     * Blade because "@context" would be parsed as a Blade directive.
     */
    private function personSchema(): array
    {
        $profile = config('portfolio.profile');
        $contact = config('portfolio.contact');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile['name'],
            'jobTitle' => $profile['title'],
            'email' => 'mailto:'.$contact['email'],
            'telephone' => $contact['phone_e164'],
            'image' => asset($profile['photo']),
            'url' => route('home'),
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $profile['location']],
            'alumniOf' => ['@type' => 'CollegeOrUniversity', 'name' => config('portfolio.education.institution')],
            'knowsAbout' => config('portfolio.tools'),
            'sameAs' => collect(config('portfolio.social'))->pluck('url')->all(),
        ];
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function services(): View
    {
        $services = collect(config('portfolio.services'));
        $groups = collect(config('portfolio.service_groups'))
            ->map(fn ($group) => $group + ['services' => $services->where('group', $group['name'])->values()])
            ->filter(fn ($group) => $group['services']->isNotEmpty());

        // Services whose group no longer exists still get shown.
        $orphans = $services->whereNotIn('group', $groups->pluck('name'))->values();
        if ($orphans->isNotEmpty()) {
            $groups->push(['name' => 'More services', 'title' => 'More *services*', 'text' => '', 'services' => $orphans]);
        }

        return view('pages.services', ['groups' => $groups->values()]);
    }

    public function portfolio(): View
    {
        return view('pages.portfolio', [
            'items' => config('portfolio.portfolio'),
            'filters' => config('portfolio.portfolio_filters'),
        ]);
    }

    public function results(): View
    {
        return view('pages.results', config('portfolio.results') + [
            'milestones' => array_reverse(config('portfolio.experience')),
        ]);
    }

    public function international(): View
    {
        $home = config('portfolio.international.home');
        $markets = $this->markets();

        return view('pages.international', [
            'home' => $home,
            'regions' => config('portfolio.international.regions'),
            'remote' => config('portfolio.international.remote'),
            'markets' => $markets,
            // Great-circle distance from home to each market's reference city.
            'routes' => $markets
                ->map(fn ($m) => $m + ['km' => $this->distanceKm($home['lat'], $home['lng'], $m['lat'], $m['lng'])])
                ->filter(fn ($m) => $m['km'] > 5)
                ->sortBy('km')
                ->values(),
        ]);
    }

    /** Every country across all regions, in region order. */
    private function markets(): Collection
    {
        return collect(config('portfolio.international.regions'))->flatMap(fn ($region) => $region['countries'] ?? [])->values();
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round(6371 * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}
