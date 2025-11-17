<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentEnrolledToClass extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $className,
        public ?string $streamName = null,
        public ?string $status = 'active',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $title = __('You have been enrolled to :class', ['class' => $this->className]);
        $line2 = $this->streamName ? __('Stream: :stream', ['stream' => $this->streamName]) : null;
        $status = $this->status ? __('Status: :status', ['status' => ucfirst($this->status)]) : null;

        $mail = (new MailMessage)
            ->subject($title)
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line($title);

        if ($line2) { $mail->line($line2); }
        if ($status) { $mail->line($status); }

        return $mail
            ->action(__('Open Student Dashboard'), route('tenant.student.dashboard'))
            ->line(__('If anything looks incorrect, please contact the school administrator.'));
    }
}
