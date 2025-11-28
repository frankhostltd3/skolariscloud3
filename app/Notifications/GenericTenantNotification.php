<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class GenericTenantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $type;
    public $channels;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info', $channels = ['database'])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello ' . $notifiable->name . ',');

        // Use HtmlString to prevent escaping of rich text content
        $mail->line(new HtmlString($this->message));

        if ($this->type === 'warning' || $this->type === 'danger') {
            $mail->level('error');
        }

        $mail->action('View Dashboard', url('/dashboard'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }
}
