<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your NCIP-EAP portal email')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'NCIP Scholar') . '!')
            ->line('Salamat for registering with the NCIP Educational Assistance Program portal.')
            ->line('Please confirm your email address to unlock your dashboard, upload requirements, and track your application status.')
            ->action('Verify email address', $verificationUrl)
            ->line('For your security, this link will expire in 60 minutes. If it expires, simply request a new verification email from the portal.')
            ->line('If you did not create this account, you can safely ignore this message.')
            ->salutation('— NCIP-EAP Support Team');
    }
}

