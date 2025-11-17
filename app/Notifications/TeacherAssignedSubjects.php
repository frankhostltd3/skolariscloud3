<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeacherAssignedSubjects extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $className,
        /** @var array<int,string> */
        public array $subjectNames,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $list = implode(', ', $this->subjectNames);
        return (new MailMessage)
            ->subject(__('New Subject Assignments'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('You have been assigned the following subjects in :class:', ['class' => $this->className]))
            ->line($list ?: __('(no subjects listed)'))
            ->action(__('View Timetable'), route('tenant.teacher.timetable.index'))
            ->line(__('Please review and update your lesson plans accordingly.'));
    }
}
