<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return match ($notifiable->type) {
            'email' => ['mail'],
            'discord' => ['discord'],
            'webhook' => ['webhook'],
            default => [],
        };
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Test Notification - UptimeCore')
            ->line('This is a test notification from UptimeCore.');
    }

    public function toDiscord($notifiable)
    {
        return [
            'content' => '🧪 Test Notification',
            'embeds' => [
                [
                    'title' => 'UptimeCore Test',
                    'description' => 'This is a test notification from UptimeCore.',
                    'color' => 0x10b981,
                ],
            ],
        ];
    }

    public function toWebhook($notifiable)
    {
        return [
            'message' => 'Test notification from UptimeCore',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

