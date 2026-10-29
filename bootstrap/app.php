<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

// On Wasmer Edge storage/ lives on a volume (LARAVEL_STORAGE_PATH) that starts
// empty. Laravel needs these folders before it loads its config, so make them here.
if ($storage = $_ENV['LARAVEL_STORAGE_PATH'] ?? $_SERVER['LARAVEL_STORAGE_PATH'] ?? null) {
    foreach (['app/public/uploads', 'app/private', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
        is_dir("{$storage}/{$dir}") || @mkdir("{$storage}/{$dir}", 0775, true);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The only logged-in area is the admin panel.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
        // Hosting (e.g. Wasmer Edge) ends HTTPS at its proxy; trust it so links stay https://.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // An admin upload bigger than PHP's post_max_size: explain instead of a 413 page.
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            if ($request->is('admin/*')) {
                return back()->with('error', 'That upload is larger than the server allows ('.ini_get('post_max_size').' per save). Try a smaller file.');
            }
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
