<?php

namespace App\Notifications;

use App\Models\ExerciseSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct(ExerciseSubmission $submission)
    {
        $this->submission = $submission;
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
        $exercise = $this->submission->exercise;
        $percentage = round(($this->submission->score / $exercise->max_score) * 100, 1);
        
        return (new MailMessage)
            ->subject('Assignment Graded: ' . $exercise->title)
            ->line('Your assignment has been graded.')
            ->line('**Assignment:** ' . $exercise->title)
            ->line('**Score:** ' . $this->submission->score . ' / ' . $exercise->max_score . ' (' . $percentage . '%)')
            ->line('**Feedback:** ' . ($this->submission->feedback ?? 'No feedback provided'))
            ->action('View Submission', route('tenant.student.classroom.exercises.show', $exercise))
            ->line('Keep up the good work!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'exercise_id' => $this->submission->exercise_id,
            'submission_id' => $this->submission->id,
            'title' => $this->submission->exercise->title,
            'score' => $this->submission->score,
            'max_score' => $this->submission->exercise->max_score,
            'feedback' => $this->submission->feedback,
            'type' => 'assignment_graded',
        ];
    }
}
