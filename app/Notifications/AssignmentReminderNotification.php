<?php

namespace App\Notifications;

use App\Models\Exercise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $exercise;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exercise $exercise)
    {
        $this->exercise = $exercise;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $hoursRemaining = now()->diffInHours($this->exercise->due_date);
        
        return (new MailMessage)
            ->subject('Reminder: Assignment Due Soon - ' . $this->exercise->title)
            ->line('This is a reminder that you have a pending assignment.')
            ->line('**Assignment:** ' . $this->exercise->title)
            ->line('**Subject:** ' . $this->exercise->subject->name)
            ->line('**Due Date:** ' . $this->exercise->due_date->format('F d, Y h:i A'))
            ->line('**Time Remaining:** ' . $hoursRemaining . ' hours')
            ->action('Submit Assignment', route('tenant.student.classroom.exercises.show', $this->exercise))
            ->line('Please submit your work before the deadline to avoid late penalties.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'exercise_id' => $this->exercise->id,
            'title' => $this->exercise->title,
            'subject' => $this->exercise->subject->name,
            'due_date' => $this->exercise->due_date,
            'type' => 'assignment_reminder',
        ];
    }
}
