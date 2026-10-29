<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Create the admin account (once). The password comes from ADMIN_PASSWORD
     * in .env, or is generated and printed so it can be changed after login.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', config('portfolio.contact.email'));

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("Admin account {$email} already exists.");

            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::password(14, symbols: false);

        User::query()->create([
            'name' => config('portfolio.profile.name'),
            'email' => $email,
            'password' => $password,
        ]);

        $this->command?->warn("Admin login → {$email} / {$password}");
        $this->command?->warn('Sign in at /admin/login and change this password under Settings.');
    }
}
