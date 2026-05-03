<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expiry = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Password Reset Request — COP Abirem CMS')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset the password for your Church of Pentecost Abirem CMS account.')
            ->action('Reset My Password', $resetUrl)
            ->line('This link will expire in **' . $expiry . ' minutes**.')
            ->line('If you did not request a password reset, no action is needed — your password will remain unchanged.')
            ->line('For security, never share this link with anyone.')
            ->salutation('God bless you, ' . PHP_EOL . 'COP Abirem Admin Team');
    }
}
