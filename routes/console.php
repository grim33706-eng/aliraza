<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recovery: `php artisan admin:password` sets a new random password for the admin.
Artisan::command('admin:password {email? : Admin email (defaults to the first account)}', function (?string $email = null) {
    $user = $email ? User::query()->where('email', $email)->first() : User::query()->oldest('id')->first();
    if (! $user) {
        $this->error('No admin account found. Run: php artisan db:seed');

        return 1;
    }

    $password = Str::password(14, symbols: false);
    $user->update(['password' => $password]);

    $this->info("New password for {$user->email}: {$password}");
    $this->line('Sign in at /admin/login and change it under Settings.');

    return 0;
})->purpose('Reset the admin panel password');
