<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentUploadedDocument extends Notification
{
    use Queueable;

    protected $student;

    protected $documentType;

    /**
     * Create a new notification instance.
     */
    public function __construct($student, $documentType)
    {
        $this->student = $student;
        $this->documentType = $documentType;
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
        return [
            'message' => 'Student '.$this->student->first_name.' '.$this->student->last_name.' uploaded a document: '.$this->documentType,
            'student_id' => $this->student->id,
            'document_type' => $this->documentType,
        ];
    }
}
