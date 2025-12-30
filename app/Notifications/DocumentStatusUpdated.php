<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdated extends Notification
{
    use Queueable;

    protected $document;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($document, $status)
    {
        $this->document = $document;
        $this->status = $status;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';
        $documentName = str_replace('_', ' ', ucwords($this->document->type, '_'));

        if ($this->status === 'approved') {
            $message = 'Your document "' . $documentName . '" has been approved by the admin.';
        } elseif ($this->status === 'rejected') {
            $message = 'Your document "' . $documentName . '" has been rejected by the admin.';
        } else {
            $message = 'The status of your document "' . $documentName . '" has been updated to pending.';
        }
        
        return [
            'type' => 'document_status',
            'title' => 'Document Status Updated',
            'message' => $message,
            'document_id' => $this->document->id,
            'status' => $this->status,
            'priority' => $this->status === 'rejected' ? 'urgent' : 'normal',
        ];
    }
}
