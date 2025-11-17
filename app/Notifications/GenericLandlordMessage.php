<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericLandlordMessage extends Notification
{
    use Queueable;

    public function __construct(public array $payload)
    {
    }

    public function via($notifiable): array
    {
        // Channels are decided by routeNotificationFor* or on-demand routes
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->payload['title'] ?? 'Notification')
            ->line($this->payload['message'] ?? '');
    }

    public function toArray($notifiable): array
    {
        return $this->payload;
    }
}
