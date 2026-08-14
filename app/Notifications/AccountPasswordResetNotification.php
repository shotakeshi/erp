<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountPasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $password,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.password_reset.subject'))
            ->greeting(__('mail.password_reset.greeting', ['name' => $notifiable->name]))
            ->line(__('mail.password_reset.description'))
            ->line(__('mail.password_reset.password', ['password' => $this->password]))
            ->line(__('mail.password_reset.warning'));
    }
}