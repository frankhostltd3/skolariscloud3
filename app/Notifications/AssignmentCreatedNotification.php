<?php

namespace App\Notifications;

use App\Models\Exercise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentCreatedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('New Assignment: ' . $this->exercise->title)
            ->line('A new assignment has been posted for your class.')
            ->line('**Assignment:** ' . $this->exercise->title)
            ->line('**Subject:** ' . $this->exercise->subject->name)
            ->line('**Due Date:** ' . $this->exercise->due_date->format('F d, Y h:i A'))
            ->line('**Max Score:** ' . $this->exercise->max_score . ' points')
            ->action('View Assignment', route('tenant.student.classroom.exercises.show', $this->exercise))
            ->line('Please complete and submit before the due date.');
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
            'max_score' => $this->exercise->max_score,
            'type' => 'assignment_created',
        ];
    }
}
