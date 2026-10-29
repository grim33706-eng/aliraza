<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\User;
use App\Notifications\AdminResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret-pass-1']);
    }

    /** Index of a section in config/admin.php by its content key. */
    private function sectionIndex(string $page, string $key): int
    {
        return array_search($key, array_column(config("admin.pages.{$page}.sections"), 'key'), true);
    }

    /* ---------------- Authentication ---------------- */

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/pages/home')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Welcome back');
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'secret-pass-1'])
            ->assertRedirect('/admin')
            ->assertSessionHas('toast.type', 'success');
        $this->assertAuthenticated();

        $this->post('/admin/logout')->assertRedirect('/admin/login')->assertSessionHas('toast.title', 'Signed out');
        $this->assertGuest();
        $this->get('/admin/login')->assertSee('Signed out');
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_forgotten_password_can_be_reset_by_email(): void
    {
        Notification::fake();
        $user = $this->admin();

        $this->get('/admin/login')->assertSee('Forgot password?');
        $this->get('/admin/forgot-password')->assertOk()->assertSee('Forgot your password?');

        $this->post('/admin/forgot-password', ['email' => 'admin@example.com'])
            ->assertRedirect('/admin/forgot-password')
            ->assertSessionHas('reset_sent');

        $token = null;
        Notification::assertSentTo($user, AdminResetPassword::class, function ($n) use (&$token, $user) {
            $token = $n->token;
            $html = $n->toMail($user)->render();

            return str_contains($html, '/admin/reset-password/'.$token);
        });

        $this->get("/admin/reset-password/{$token}?email=admin@example.com")->assertOk()->assertSee('Choose a new password');

        $this->post('/admin/reset-password', ['token' => 'wrong', 'email' => 'admin@example.com', 'password' => 'brandnew99', 'password_confirmation' => 'brandnew99'])
            ->assertSessionHasErrors('email');

        $this->post('/admin/reset-password', ['token' => $token, 'email' => 'admin@example.com', 'password' => 'brandnew99', 'password_confirmation' => 'brandnew99'])
            ->assertRedirect('/admin/login')
            ->assertSessionHas('toast.title', 'Password updated');

        $this->assertTrue(Hash::check('brandnew99', $user->fresh()->password));
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'brandnew99'])->assertRedirect('/admin');
    }

    public function test_reset_request_does_not_reveal_unknown_emails(): void
    {
        Notification::fake();
        $this->admin();

        $this->post('/admin/forgot-password', ['email' => 'stranger@example.com'])
            ->assertRedirect('/admin/forgot-password')
            ->assertSessionHas('reset_sent');

        Notification::assertNothingSent();
    }

    public static function adminPages(): array
    {
        $slugs = array_keys((require __DIR__.'/../../config/admin.php')['pages']);

        return array_combine($slugs, array_map(fn ($slug) => [$slug], $slugs));
    }

    #[DataProvider('adminPages')]
    public function test_every_page_editor_renders(string $slug): void
    {
        $this->actingAs($this->admin())->get("/admin/pages/{$slug}")->assertOk()->assertSee('Save section');
    }

    public function test_dashboard_messages_and_settings_render(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin')->assertOk()->assertSee('Website pages');
        $this->get('/admin/messages')->assertOk();
        $this->get('/admin/settings')->assertOk()->assertSee('Change password');
    }

    /* ---------------- Content editing ---------------- */

    public function test_editing_a_section_updates_the_live_site(): void
    {
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('home', 'pages.home.hero');
        $hero = config('portfolio.pages.home.hero');
        $hero['title'] = 'Brand new *headline* from the admin';

        $this->put("/admin/pages/home/{$index}", ['data' => $hero])
            ->assertRedirect()
            ->assertSessionHas('toast.title', 'Changes saved');

        $this->assertDatabaseHas('contents', ['key' => 'pages.home.hero']);
        $this->get('/')->assertSee('Brand new <em>headline</em> from the admin', false);
    }

    public function test_restoring_a_section_brings_back_the_original(): void
    {
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('contact', 'pages.contact.hero');
        $original = config('portfolio.pages.contact.hero.lead');

        $this->put("/admin/pages/contact/{$index}", ['data' => ['lead' => 'Temporary lead'] + config('portfolio.pages.contact.hero')]);
        $this->get('/contact')->assertSee('Temporary lead');

        $this->delete("/admin/pages/contact/{$index}")->assertRedirect();
        $this->assertDatabaseMissing('contents', ['key' => 'pages.contact.hero']);
        $this->get('/contact')->assertSee(e($original), false);
    }

    public function test_invalid_input_is_rejected_and_nothing_is_saved(): void
    {
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('contact', 'contact');

        $this->from('/admin/pages/contact')
            ->put("/admin/pages/contact/{$index}", ['data' => ['email' => 'not-an-email', 'whatsapp' => 'wa.me/123'] + config('portfolio.contact')])
            ->assertRedirect('/admin/pages/contact#section-'.$index)
            ->assertSessionHasErrors(['data.email', 'data.whatsapp']);

        $this->assertSame(0, Content::query()->count());
    }

    public function test_repeater_items_can_be_reordered_and_removed(): void
    {
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('services', 'process');
        [$plan, $create] = config('portfolio.process');

        // Keep only two steps, in reverse order.
        $this->put("/admin/pages/services/{$index}", ['data' => ['n2' => $create, 'n1' => $plan]])->assertRedirect();

        $this->assertSame([$create['title'], $plan['title']], array_column(config('portfolio.process'), 'title'));
        $this->get('/services')->assertSeeInOrder([$create['title'], $plan['title']]);
    }

    public function test_images_can_be_uploaded_and_are_optimised(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('about', 'profile');

        $this->put("/admin/pages/about/{$index}", [
            'data' => config('portfolio.profile'),
            'files' => ['photo' => UploadedFile::fake()->image('me.jpg', 2400, 1600)],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $path = config('portfolio.profile.photo');
        $this->assertStringStartsWith('storage/uploads/', $path);
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $path));
        [$width] = getimagesize(Storage::disk('public')->path(str_replace('storage/', '', $path)));
        $this->assertSame(2000, $width);

        $this->get('/about')->assertSee($path);
    }

    public function test_portfolio_upload_fills_in_dimensions(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $index = $this->sectionIndex('portfolio', 'portfolio');
        $items = config('portfolio.portfolio');

        $this->put("/admin/pages/portfolio/{$index}", [
            'data' => $items,
            'files' => [0 => ['image' => UploadedFile::fake()->image('shot.png', 1200, 600)]],
        ])->assertSessionHasNoErrors();

        $first = config('portfolio.portfolio.0');
        $this->assertSame([1200, 600], [$first['width'], $first['height']]);
    }

    /* ---------------- Messages ---------------- */

    public function test_contact_form_messages_reach_the_inbox(): void
    {
        Mail::fake();
        $this->post('/contact', ['name' => 'Jane Client', 'email' => 'jane@example.com', 'subject' => 'Need ads', 'message' => 'Please help with our Meta campaigns.']);

        $message = ContactMessage::query()->sole();
        $this->assertNull($message->read_at);

        $this->actingAs($this->admin());
        $this->get('/admin/messages')->assertSee('Jane Client')->assertSee('Need ads');
        $this->get("/admin/messages/{$message->id}")->assertOk()->assertSee('Please help with our Meta campaigns.');
        $this->assertNotNull($message->fresh()->read_at);

        $this->patch("/admin/messages/{$message->id}/read");
        $this->assertNull($message->fresh()->read_at);

        $this->delete("/admin/messages/{$message->id}")->assertRedirect('/admin/messages');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    /* ---------------- Settings ---------------- */

    public function test_password_change_requires_the_current_password(): void
    {
        $user = $this->admin();
        $this->actingAs($user);

        $this->put('/admin/settings/password', ['current_password' => 'nope', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertSessionHasErrorsIn('password', 'current_password');

        $this->put('/admin/settings/password', ['current_password' => 'secret-pass-1', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertRedirect('/admin/settings');

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }

    public function test_profile_and_photo_can_be_updated(): void
    {
        Storage::fake('public');
        $user = $this->admin();

        $this->actingAs($user)->put('/admin/settings/profile', [
            'name' => 'Raza S.',
            'email' => 'raza@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.png', 800, 600),
        ])->assertRedirect('/admin/settings');

        $user->refresh();
        $this->assertSame(['Raza S.', 'raza@example.com'], [$user->name, $user->email]);
        $this->assertStringStartsWith('storage/uploads/', $user->avatar);
        [$w, $h] = getimagesize(Storage::disk('public')->path(str_replace('storage/', '', $user->avatar)));
        $this->assertSame([400, 400], [$w, $h]);

        $this->get('/admin')->assertSee($user->avatar);
    }
}
