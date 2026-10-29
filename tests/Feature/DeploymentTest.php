<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AutoMigrate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Behaviour the Wasmer Edge deployment relies on (see deploy/wasmer). */
class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        File::delete(File::glob(storage_path('framework/migrated-*')));
        parent::tearDown();
    }

    public function test_uploads_are_served_by_laravel_when_storage_is_not_linked(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/2026/10/photo.webp', 'image-bytes');

        $this->get('/storage/uploads/2026/10/photo.webp')
            ->assertOk()
            ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');

        $this->get('/storage/uploads/2026/10/missing.webp')->assertNotFound();
        $this->get('/storage/uploads/../../.env')->assertNotFound();
    }

    public function test_auto_migrate_sets_up_the_database_once(): void
    {
        config(['app.auto_migrate' => true]);
        putenv('ADMIN_PASSWORD=first-pass-123');

        AutoMigrate::run($this->app);
        $this->assertCount(1, File::glob(storage_path('framework/migrated-*')));
        $admin = User::query()->sole();

        // Later requests leave everything alone, even if the admin changes.
        $admin->update(['name' => 'Changed']);
        $this->get('/')->assertOk();
        $this->assertSame('Changed', User::query()->sole()->name);

        putenv('ADMIN_PASSWORD');
    }
}
