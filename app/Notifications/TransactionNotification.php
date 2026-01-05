<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification
{
    use Queueable;

    protected $title;

    protected $message;

    protected $actionType;

    protected $priority;

    /**
     * Create a new notification instance.
     */
    public function __construct($actionType, $title, $message, $priority = 'normal')
    {
        $this->actionType = $actionType;
        $this->title = $title;
        $this->message = $message;
        $this->priority = $priority;
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
        return [
            'type' => $this->actionType,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
        ];
    }
}
