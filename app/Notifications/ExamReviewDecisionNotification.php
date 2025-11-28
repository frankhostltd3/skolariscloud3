<?php

namespace App\Notifications;

use App\Models\OnlineExam;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamReviewDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly OnlineExam $exam,
        private readonly string $decision,
        private readonly ?string $notes = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->mailSubject())
            ->greeting(__('Hello :name,', ['name' => $notifiable->name ?? __('Teacher')]))
            ->line($this->primaryLine())
            ->line(__('Exam: :title', ['title' => $this->exam->title]))
            ->line(__('Schedule: :start → :end', [
                'start' => optional($this->exam->start_time)->format('M d, Y h:i A') ?? __('Not set'),
                'end' => optional($this->exam->end_time)->format('M d, Y h:i A') ?? __('Not set'),
            ]));

        if ($this->notes) {
            $mailMessage->line(__('Reviewer notes:'));
            $mailMessage->line($this->notes);
        }

        $mailMessage->action(__('View exam'), route('tenant.teacher.classroom.exams.show', $this->exam))
            ->line(__('Thanks for keeping your assessments up to date.'));

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'exam_id' => $this->exam->id,
            'title' => $this->exam->title,
            'decision' => $this->decision,
            'notes' => $this->notes,
            'status' => $this->exam->approval_status,
        ];
    }

    private function primaryLine(): string
    {
        return match ($this->decision) {
            'approved' => __('Your exam has been approved and scheduled.'),
            'changes_requested' => __('More revisions are required before this exam can be approved.'),
            'rejected' => __('This exam was rejected and archived.'),
            'activated' => __('Your exam has been manually activated and is now live for students.'),
            'completed' => __('Your exam window has been closed automatically.'),
            default => __('There is an update regarding your exam.'),
        };
    }

    private function mailSubject(): string
    {
        return match ($this->decision) {
            'approved' => __('Exam approved: :title', ['title' => $this->exam->title]),
            'changes_requested' => __('Changes requested: :title', ['title' => $this->exam->title]),
            'rejected' => __('Exam rejected: :title', ['title' => $this->exam->title]),
            'activated' => __('Exam activated: :title', ['title' => $this->exam->title]),
            'completed' => __('Exam window completed: :title', ['title' => $this->exam->title]),
            default => __('Exam update: :title', ['title' => $this->exam->title]),
        };
    }
}
