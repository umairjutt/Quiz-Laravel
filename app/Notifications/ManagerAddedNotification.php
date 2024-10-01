<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class ManagerAddedNotification extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url("/password-reset/{$notifiable->id}?token={$this->token}");

        return (new MailMessage)
                    ->subject('Set Up Your Password')
                    ->line('You have been added as a Manager. Click the link below to set up your password.')
                    ->action('Set Password', $url)
                    ->line('This link will expire in 24 hours.');
    }
}
