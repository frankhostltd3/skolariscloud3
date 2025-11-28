<?php

namespace App\Notifications;

use App\Models\OnlineExam;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamSubmittedForReviewNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly OnlineExam $exam)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teacherName = $this->exam->teacher?->name ?? __('A teacher');
        $className = $this->exam->class?->name;
        $subjectName = $this->exam->subject?->name;

        return (new MailMessage)
            ->subject(__('New exam submitted for review: :title', ['title' => $this->exam->title]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? __('Admin')]))
            ->line(__(':teacher submitted an exam that needs your approval.', ['teacher' => $teacherName]))
            ->line(__('Exam: :title', ['title' => $this->exam->title]))
            ->line(__(
                'Class/Subject: :class / :subject',
                [
                    'class' => $className ?? __('Unknown Class'),
                    'subject' => $subjectName ?? __('Unknown Subject'),
                ]
            ))
            ->line(__('Window: :start to :end', [
                'start' => optional($this->exam->start_time)->format('M d, Y h:i A') ?? __('Not set'),
                'end' => optional($this->exam->end_time)->format('M d, Y h:i A') ?? __('Not set'),
            ]))
            ->action(__('Review exam'), route('admin.exams.show', $this->exam))
            ->line(__('Thank you for keeping assessments compliant.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'exam_id' => $this->exam->id,
            'title' => $this->exam->title,
            'teacher' => $this->exam->teacher?->only(['id', 'name', 'email']),
            'submitted_for_review_at' => $this->exam->submitted_for_review_at,
            'status' => $this->exam->approval_status,
        ];
    }
}
