<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherAssignedToClass extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $className,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('You have been assigned as Class Teacher'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('You have been assigned as the class teacher for :class.', ['class' => $this->className]))
            ->action(__('Open Teacher Dashboard'), route('tenant.teacher.dashboard'))
            ->line(__('If this was unexpected, please reach out to the administrator.'));
    }
}
