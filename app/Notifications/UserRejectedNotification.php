<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Route;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $contactUrl = Route::has('tenant.contact') ? route('tenant.contact') : url('/contact');

        return (new MailMessage)
            ->subject('Account Registration Status')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thank you for your interest in registering with us.')
            ->line('Unfortunately, we are unable to approve your registration at this time.')
            ->line('**Reason:** ' . $this->reason)
            ->line('If you believe this is an error or have questions, please contact your administrator.')
            ->action('Contact Support', $contactUrl)
            ->line('We appreciate your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
