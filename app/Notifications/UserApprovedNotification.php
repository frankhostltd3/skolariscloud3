<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = route('tenant.login');
        // Helpful links (role-aware)
        $feesOverviewUrl = route('tenant.student.fees.index');
        $parentDashboardUrl = route('tenant.parent');

        $mail = (new MailMessage)
            ->subject('Your Account Has Been Approved!')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Great news! Your account registration has been approved.')
            ->line('You now have full access to the system.')
            ->action('Login to Your Account', $loginUrl)
            ->line('If you have any questions, please contact your administrator.')
            ->line('Thank you for joining us!');

        // Add payment guidance section if roles likely include student or parent
        try {
            if (method_exists($notifiable, 'hasRole')) {
                if ($notifiable->hasRole('Student') || $notifiable->hasRole('student')) {
                    $mail->line('Next steps:');
                    $mail->line('• Review your fees and make a payment using your preferred method (mobile money, card, or bank).');
                    $mail->action('Open Fees & Payments', $feesOverviewUrl);
                    $mail->line('If you prefer bank deposit, you can download a bank payment slip from the Fees page after login.');
                } elseif ($notifiable->hasRole('Parent') || $notifiable->hasRole('parent')) {
                    $mail->line('Next steps for Parents/Guardians:');
                    $mail->line('• Open your dashboard to view your wards and their outstanding fees, then proceed to payment.');
                    $mail->action('Open Parent Dashboard', $parentDashboardUrl);
                }
            }
        } catch (\Throwable $e) {
            // Fail silently; continue with basic email
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
