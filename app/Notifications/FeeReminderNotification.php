<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $type; // 'due_soon' or 'overdue'

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, $type = 'due_soon')
    {
        $this->invoice = $invoice;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $schoolName = setting('school_name', config('app.name'));
        $dueDate = $this->invoice->due_date->format('d M Y');
        $amount = format_money($this->invoice->balance);

        $subject = $this->type === 'overdue'
            ? "URGENT: Overdue Fees Reminder - {$schoolName}"
            : "Fee Payment Reminder - {$schoolName}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Dear Parent/Guardian,');

        if ($this->type === 'overdue') {
            $message->line("This is a reminder that the payment for invoice #{$this->invoice->invoice_number} is OVERDUE.");
        } else {
            $message->line("This is a reminder that the payment for invoice #{$this->invoice->invoice_number} is due soon.");
        }

        $message->line("Student: " . ($this->invoice->student->name ?? 'N/A'))
            ->line("Description: " . ($this->invoice->feeStructure->name ?? 'Fees'))
            ->line("Due Date: {$dueDate}")
            ->line("Outstanding Balance: {$amount}")
            ->action('View Invoice & Pay', route('tenant.parent.fees.index')) // Assuming parent portal link
            ->line('Please ensure payment is made to avoid any inconvenience.')
            ->line('Thank you for choosing ' . $schoolName);

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->balance,
            'due_date' => $this->invoice->due_date,
            'type' => $this->type,
            'message' => $this->type === 'overdue'
                ? "Invoice #{$this->invoice->invoice_number} is overdue."
                : "Invoice #{$this->invoice->invoice_number} is due soon.",
        ];
    }
}
