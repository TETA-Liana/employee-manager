<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Password Reset OTP - ' . config('app.name'))
            ->greeting('Hi ' . ($notifiable->name ?? 'there') . ',')
            ->line('You recently requested to reset your password for your ' . config('app.name') . ' account.')
            ->line('Please use the following **6-digit One-Time Password (OTP)** to complete the process:')
            ->line('```')
            ->line($this->token)
            ->line('```')
            ->line('This OTP will expire in ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) . ' minutes.')
            ->line('If you didn\'t request this, you can safely ignore this email.')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }



}
