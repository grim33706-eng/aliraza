<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::controller(PageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/services', 'services')->name('services');
    Route::get('/portfolio', 'portfolio')->name('portfolio');
    Route::get('/results', 'results')->name('results');
    Route::get('/international', 'international')->name('international');
});

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Admin uploads (storage/uploads/...). Locally the public/storage link serves them
// directly; on Wasmer Edge storage lives on a volume, so Laravel streams them.
Route::get('/storage/uploads/{path}', function (string $path) {
    $file = 'uploads/'.$path;
    abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($file), 404);

    // Upload names are random and never reused, so browsers may cache them for good.
    return Storage::disk('public')->response($file, headers: ['Cache-Control' => 'public, max-age=31536000, immutable']);
})->where('path', '.*')->name('uploads');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');

        Route::get('forgot-password', [Admin\PasswordResetController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [Admin\PasswordResetController::class, 'store'])->middleware('throttle:admin-reset')->name('password.email');
        Route::get('reset-password/{token}', [Admin\PasswordResetController::class, 'edit'])->name('password.reset');
        Route::post('reset-password', [Admin\PasswordResetController::class, 'update'])->middleware('throttle:admin-reset')->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('pages/{page}', [Admin\ContentController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}/{section}', [Admin\ContentController::class, 'update'])->whereNumber('section')->name('pages.update');
        Route::delete('pages/{page}/{section}', [Admin\ContentController::class, 'reset'])->whereNumber('section')->name('pages.reset');

        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
        Route::patch('messages/{message}/read', [Admin\MessageController::class, 'toggleRead'])->name('messages.toggle');
        Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');

        Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::put('settings/profile', [Admin\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('settings/password', [Admin\SettingsController::class, 'updatePassword'])->name('settings.password');
    });
});
