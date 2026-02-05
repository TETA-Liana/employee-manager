<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '! 🚀')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('We\'re thrilled to have you join us! Your account has been successfully created and is ready to use.')
            ->line('To get you started immediately, we\'ve generated your secure access token below.')
            ->line('**Your Credentials:**')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Your Access Token:**')
            ->line('```')
            ->line($this->token)
            ->line('```')
            ->line('Include this token in the `Authorization` header as a `Bearer` token to interact with our API.')
            ->action('Explore Dashboard', url('/'))
            ->line('If you have any questions, feel free to reply to this email.')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
