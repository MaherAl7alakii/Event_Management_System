<?php

namespace App\Services;

use App\Notifications\AppNotification;

class NotificationDispatcher
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {
    }

    public function dispatch(AppNotification $notification): void
    {
        foreach ($notification->recipients() as $recipient) {
            $content = $notification->messageFor($recipient);


            $this->notifications->send(
                user: $recipient,
                type: $notification->type()->value,
                title: $content['title'],
                message: $content['message'],
                objectId: $content['object_id'] ?? null,
            );
        }
    }
}
