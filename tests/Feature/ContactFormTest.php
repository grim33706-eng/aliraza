<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    private function valid(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Client',
            'email' => 'jane@example.com',
            'subject' => 'Meta Ads for my business',
            'message' => 'Hi Raza, I would like help with lead generation campaigns.',
        ], $overrides);
    }

    public function test_valid_submission_sends_mail_and_redirects_with_success(): void
    {
        Mail::fake();

        $this->post('/contact', $this->valid())
            ->assertRedirect('/contact')
            ->assertSessionHas('contact_success');

        Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) {
            return $mail->hasTo(config('portfolio.mail.inbox'))
                && $mail->hasReplyTo('jane@example.com')
                && $mail->data['subject'] === 'Meta Ads for my business';
        });
    }

    public function test_json_submission_returns_success_message(): void
    {
        Mail::fake();

        $this->postJson('/contact', $this->valid())
            ->assertOk()
            ->assertJsonStructure(['message']);
    }

    public function test_invalid_submission_returns_field_errors(): void
    {
        Mail::fake();

        $this->from('/contact')
            ->post('/contact', $this->valid(['name' => '', 'email' => 'not-an-email', 'message' => 'short']))
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->postJson('/contact', ['subject' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);

        Mail::assertNothingSent();
    }

    public function test_errors_are_rendered_on_the_form(): void
    {
        $this->from('/contact')
            ->followingRedirects()
            ->post('/contact', $this->valid(['email' => 'nope']))
            ->assertSee('That email address doesn&#039;t look quite right.', false)
            ->assertSee('value="Jane Client"', false);
    }

    public function test_honeypot_submission_is_silently_dropped(): void
    {
        Mail::fake();

        $this->post('/contact', $this->valid(['website' => 'http://spam.example']))
            ->assertSessionHas('contact_success');

        Mail::assertNothingSent();
    }

    public function test_csrf_token_is_rendered(): void
    {
        $this->get('/contact')->assertSee('name="_token"', false);
    }

    public function test_submissions_are_rate_limited(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/contact', $this->valid())->assertOk();
        }

        $this->postJson('/contact', $this->valid())->assertStatus(429);
    }
}
