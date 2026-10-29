<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Support\AutoMigrate;
use App\Support\ContentRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ContentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.auto_migrate') && ! $this->app->runningInConsole()) {
            AutoMigrate::run($this->app);
        }

        // Lay the admin's edits over the default content in config/portfolio.php.
        $this->app->make(ContentRepository::class)->apply();

        // Contact form: a handful of messages per minute per visitor is plenty.
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(5)->by($request->ip())->response(
            fn () => $request->expectsJson()
                ? response()->json(['message' => 'Too many messages in a short time — please wait a minute and try again.'], 429)
                : back()->withInput()->with('contact_error', 'Too many messages in a short time — please wait a minute and try again.')
        ));

        // Admin login: slow down password guessing.
        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)
            ->by(strtolower((string) $request->input('email')).'|'.$request->ip())
            ->response(fn () => back()->onlyInput('email')->withErrors(['email' => 'Too many sign-in attempts. Please wait a minute and try again.'])));

        // Password reset requests: a few per hour is plenty for one admin.
        RateLimiter::for('admin-reset', fn (Request $request) => Limit::perMinutes(10, 5)
            ->by($request->ip())
            ->response(fn () => back()->onlyInput('email')->withErrors(['email' => 'Too many reset attempts. Please wait a few minutes and try again.'])));

        // Unread count for the admin sidebar badge.
        View::composer(['admin.*', 'components.admin.layout'], function ($view) {
            try {
                $view->with('unreadMessages', ContactMessage::query()->unread()->count());
            } catch (Throwable) {
                $view->with('unreadMessages', 0);
            }
        });
    }
}
