<?php

namespace App\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Throwable;

/**
 * Brings the database up to date on hosts where you can't run artisan
 * yourself (Wasmer Edge's free plan has no SSH). Enabled with AUTO_MIGRATE=true.
 *
 * The first request after a deploy that adds migrations runs `migrate` and
 * `db:seed` (which only creates the admin account once). A marker file named
 * after the set of migrations means every other request costs one file check.
 */
class AutoMigrate
{
    public static function run(Application $app): void
    {
        $migrations = glob($app->databasePath('migrations/*.php')) ?: [];
        $marker = $app->storagePath('framework/migrated-'.md5(implode('|', array_map('basename', $migrations))));

        if (is_file($marker)) {
            return;
        }

        // Only one request does the work; any others arriving meanwhile wait for it.
        $lock = fopen($marker.'.lock', 'c');
        try {
            flock($lock, LOCK_EX);
            if (is_file($marker)) {
                return;
            }

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);

            foreach (glob($app->storagePath('framework/migrated-*')) ?: [] as $old) {
                if (! str_ends_with($old, '.lock')) {
                    @unlink($old);
                }
            }
            touch($marker);
        } catch (Throwable $e) {
            report($e); // try again on the next request
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
            @unlink($marker.'.lock');
        }
    }
}
