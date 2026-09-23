<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('langkahkecil.frontend_url', config('app.url'));
        $resetUrl = $frontendUrl.'/reset-password?token='.$this->token.'&email='.$notifiable->email;

        return (new MailMessage)
            ->subject('Reset Password - '.config('app.name'))
            ->view('emails.reset-password', [
                'name' => $notifiable->name,
                'resetUrl' => $resetUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
