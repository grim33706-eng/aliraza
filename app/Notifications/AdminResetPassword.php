<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPassword extends Notification
{
    public function __construct(public string $token) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);

        return (new MailMessage)
            ->subject('Reset your admin password')
            ->view(['mail.password-reset', 'mail.password-reset-text'], [
                'name' => strtok($notifiable->name, ' '),
                'url' => $url,
                'expires' => config('auth.passwords.users.expire'),
                'site' => config('portfolio.profile.name'),
            ]);
    }
}
