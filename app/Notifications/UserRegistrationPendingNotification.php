<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistrationPendingNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $statusUrl = route('pending-approval');

        return (new MailMessage)
            ->subject('Registration Received - Pending Approval')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Thank you for registering with us!')
            ->line('Your account has been created and is currently **pending approval** from an administrator.')
            ->line('You will receive an email notification once your account has been reviewed.')
            ->line('This process typically takes 1-2 business days.')
            ->action('Check Status', $statusUrl)
            ->line('Thank you for your patience!');
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
