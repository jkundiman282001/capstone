<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentSubmittedApplication extends Notification
{
    use Queueable;

    protected $student;

    /**
     * Create a new notification instance.
     */
    public function __construct($student)
    {
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $type = 'application form';
        // Check if student is a grantee (Renewal)
        if ($this->student->basicInfo && strtolower(trim($this->student->basicInfo->grant_status ?? '')) === 'grantee') {
            $type = 'renewal application';
        }

        return [
            'message' => 'Student '.$this->student->first_name.' '.$this->student->last_name.' has submitted their '.$type.'.',
            'student_id' => $this->student->id,
            'title' => 'Application Submitted',
            'type' => 'submission'
        ];
    }
}
