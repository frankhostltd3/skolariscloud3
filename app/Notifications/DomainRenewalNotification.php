<?php

namespace App\Notifications;

use App\Models\DomainOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainRenewalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public DomainOrder $order;

    public function __construct(DomainOrder $order)
    {
        $this->order = $order;
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
        $daysUntilExpiry = $this->order->days_until_expiry;
        $isExpired = $daysUntilExpiry <= 0;

        $subject = $isExpired
            ? "⚠️ Domain Expired: {$this->order->full_domain}"
            : "🔔 Domain Renewal Reminder: {$this->order->full_domain}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        if ($isExpired) {
            $message->error()
                ->line("Your domain **{$this->order->full_domain}** has expired!")
                ->line("Expiry Date: {$this->order->expires_at->format('F d, Y')}")
                ->line('Please renew your domain immediately to avoid service disruption.');
        } else {
            $urgency = $daysUntilExpiry <= 7 ? 'urgent' : 'important';

            $message->line("This is an {$urgency} reminder that your domain is expiring soon.")
                ->line("**Domain:** {$this->order->full_domain}")
                ->line("**Expires:** {$this->order->expires_at->format('F d, Y')}")
                ->line("**Days Remaining:** {$daysUntilExpiry}");

            if ($this->order->auto_renew) {
                $message->line('✓ Auto-renewal is **enabled**. Your domain will be renewed automatically.');
            } else {
                $message->line('⚠️ Auto-renewal is **disabled**. Please renew manually to prevent expiration.');
            }
        }

        return $message
            ->action('View Domain Details', route('landlord.domains.orders.show', $this->order))
            ->line('Thank you for using Skolaris Cloud!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'domain_renewal',
            'order_id' => $this->order->id,
            'domain' => $this->order->full_domain,
            'expires_at' => $this->order->expires_at,
            'days_until_expiry' => $this->order->days_until_expiry,
            'auto_renew' => $this->order->auto_renew,
            'status' => $this->order->days_until_expiry <= 0 ? 'expired' : 'expiring',
        ];
    }
}
