<?php

namespace App\Notifications;

use App\Models\LandlordInvoice;
use App\Models\LandlordDunningPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class LandlordInvoiceSuspended extends Notification
{
    use Queueable;

    public function __construct(public LandlordInvoice $invoice)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'sms', 'slack', 'webhook'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->invoice;
        $policy = LandlordDunningPolicy::current();

        return (new MailMessage())
            ->markdown('mail.landlord.dunning', [
                'invoice' => $invoice,
                'policy' => $policy,
                'action' => 'suspension',
            ]);
    }

    public function toSms(object $notifiable): string
    {
        $invoice = $this->invoice;

        return __('Tenant :tenant suspended. Invoice :number unpaid. Balance $:amount.', [
            'tenant' => $invoice->tenant_name_snapshot ?? $invoice->tenant_id ?? __('Unknown tenant'),
            'number' => $invoice->invoice_number,
            'amount' => number_format((float) $invoice->total, 2),
        ]);
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        $invoice = $this->invoice;

        return (new SlackMessage())
            ->error()
            ->content(__('Tenant suspended for overdue invoice :number.', ['number' => $invoice->invoice_number]))
            ->attachment(function ($attachment) use ($invoice) {
                $attachment->title($invoice->tenant_name_snapshot ?? __('Unknown tenant'))
                    ->fields([
                        __('Due date') => optional($invoice->due_at)->toDateString() ?? __('Unknown'),
                        __('Amount') => '$' . number_format((float) $invoice->total, 2),
                        __('Status') => $invoice->status,
                    ]);
            });
    }

    public function toWebhook(object $notifiable): array
    {
        $invoice = $this->invoice;

        return [
            'event' => 'billing.invoice.suspended',
            'invoice' => [
                'id' => $invoice->getKey(),
                'number' => $invoice->invoice_number,
                'tenant_id' => $invoice->tenant_id,
                'tenant' => $invoice->tenant_name_snapshot,
                'total' => $invoice->total,
                'due_at' => optional($invoice->due_at)->toIso8601String(),
            ],
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->getKey(),
            'invoice_number' => $this->invoice->invoice_number,
            'tenant_id' => $this->invoice->tenant_id,
            'action' => 'suspended',
        ];
    }
}
