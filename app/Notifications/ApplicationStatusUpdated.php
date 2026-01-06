<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
{
    use Queueable;

    protected $status;

    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct($status, $rejectionReason = null)
    {
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
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
        if ($this->status === 'validated') {
            $message = 'Congratulations! Your scholarship application has been validated by the admin.';
        } elseif ($this->status === 'rejected') {
            $message = 'We regret to inform you that your scholarship application has been rejected.';
            if ($this->rejectionReason) {
                $message .= ' Reason: '.$this->rejectionReason;
            }
        } elseif ($this->status === 'returned') {
            $message = 'Your scholarship application has been returned by the admin due to document issues. Please review your documents and re-submit if necessary.';
        } else {
            $message = 'Your scholarship application status has been updated to pending.';
        }

        return [
            'type' => 'application_status',
            'title' => 'Application Status Updated',
            'message' => $message,
            'status' => $this->status,
            'rejection_reason' => $this->rejectionReason,
            'priority' => in_array($this->status, ['rejected', 'returned']) ? 'urgent' : ($this->status === 'validated' ? 'high' : 'normal'),
        ];
    }
}
