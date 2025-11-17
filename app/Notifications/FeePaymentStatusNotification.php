<?php

namespace App\Notifications;

use App\Models\Finance\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeePaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FeePayment $payment,
        public string $status, // 'confirmed' | 'rejected'
        public ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isConfirmed = $this->status === 'confirmed';
        $amount = format_money((float) $this->payment->amount);
        $method = ucfirst($this->payment->method);
        $reference = $this->payment->reference ?? '—';

        // Fee link if meta contains fee_id
        $actionUrl = null;
        try {
            $feeId = $this->payment->meta['fee_id'] ?? null;
            if ($feeId) {
                $actionUrl = route('tenant.student.fees.show', ['fee' => $feeId]);
            } else {
                $actionUrl = route('tenant.student.fees.index');
            }
        } catch (\Throwable $e) {
            $actionUrl = url('/app');
        }

        $mail = (new MailMessage)
            ->subject($isConfirmed ? __('Your Fee Payment Was Confirmed') : __('Your Fee Payment Was Rejected'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Payment Amount: :amount', ['amount' => $amount]))
            ->line(__('Method: :method', ['method' => $method]))
            ->line(__('Reference: :reference', ['reference' => $reference]))
            ->line($isConfirmed
                ? __('Status: Confirmed. Thank you for your payment!')
                : __('Status: Rejected. Please review the reason below and re-submit your proof if needed.'));

        if (!$isConfirmed) {
            if ($this->reason) {
                $mail->line(__('Rejection reason: :reason', ['reason' => $this->reason]));
            }
            $mail->line(__('You can upload a new bank slip proof from the Fees page.'));
        }

        if ($actionUrl) {
            $mail->action(__('Open Fees Page'), $actionUrl);
        }

        return $mail->line(__('If you have any questions, please contact the bursar.'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'status' => $this->status,
            'amount' => (float) $this->payment->amount,
            'method' => $this->payment->method,
            'reference' => $this->payment->reference,
            'reason' => $this->reason,
        ];
    }
}
