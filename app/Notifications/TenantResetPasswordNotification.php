<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class TenantResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The school instance.
     *
     * @var \App\Models\School|null
     */
    public $school;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
        $this->school = app()->bound('currentSchool') ? app('currentSchool') : null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('tenant.reset-password', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $schoolName = $this->school ? $this->school->name : config('app.name');

        return (new MailMessage)
            ->subject(Lang::get('Reset Password - :school', ['school' => $schoolName]))
            ->greeting(Lang::get('Hello :name!', ['name' => $notifiable->name]))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account at :school.', ['school' => $schoolName]))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'))
            ->salutation(Lang::get('Regards, :school Team', ['school' => $schoolName]));
    }
}
