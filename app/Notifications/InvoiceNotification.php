<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Invoice $invoice;
    protected string $recipientType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, string $recipientType = 'student')
    {
        $this->invoice = $invoice;
        $this->recipientType = $recipientType;
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
        $school = $this->invoice->school;
        $schoolName = $school?->name ?? 'School';
        $studentName = $this->invoice->student?->name ?? 'Student';
        $feeDescription = $this->invoice->feeStructure?->fee_name ?? 'School Fees';
        
        $greeting = $this->recipientType === 'parent' 
            ? "Dear Parent/Guardian of {$studentName},"
            : "Dear {$studentName},";

        $message = (new MailMessage)
            ->subject("Invoice #{$this->invoice->invoice_number} - {$schoolName}")
            ->greeting($greeting)
            ->line("Please find below the details of your invoice from {$schoolName}.")
            ->line("**Invoice Number:** {$this->invoice->invoice_number}")
            ->line("**Description:** {$feeDescription}")
            ->line("**Academic Year:** {$this->invoice->academic_year}")
            ->line("**Term:** " . ($this->invoice->term ?? 'N/A'))
            ->line("**Total Amount:** " . formatMoney($this->invoice->total_amount))
            ->line("**Paid Amount:** " . formatMoney($this->invoice->paid_amount))
            ->line("**Balance Due:** " . formatMoney($this->invoice->balance))
            ->line("**Due Date:** " . $this->invoice->due_date->format('F d, Y'));

        if ($this->invoice->balance > 0) {
            $message->line("Please ensure payment is made before the due date to avoid any inconvenience.");
        } else {
            $message->line("This invoice has been fully paid. Thank you!");
        }

        $message->line("If you have any questions, please contact the school's finance office.")
            ->salutation("Best regards,\n{$schoolName} Finance Department");

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total_amount' => $this->invoice->total_amount,
            'balance' => $this->invoice->balance,
            'due_date' => $this->invoice->due_date->toDateString(),
            'message' => "Invoice #{$this->invoice->invoice_number} for " . formatMoney($this->invoice->total_amount),
        ];
    }
}
