<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $routes = ['home', 'about', 'services', 'portfolio', 'results', 'international', 'contact'];

        return response()
            ->view('sitemap', ['urls' => array_map(fn ($name) => route($name), $routes)])
            ->header('Content-Type', 'application/xml');
    }
}
