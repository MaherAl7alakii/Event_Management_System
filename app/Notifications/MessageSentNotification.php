<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Message;
use App\Models\User;

class MessageSentNotification extends AppNotification
{
    public function __construct(
        private readonly Message $message,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::NEW_MESSAGE;
    }

    public function recipients(): array
    {

        $receiver = $this->message->conversation->otherUser($this->message->sender_id);

        return [$receiver];
    }

    public function messageFor(User $recipient): array
    {
        $sender = $this->message->sender;

        $senderName = $sender->serviceProvider?->company_name ?? $sender->name;
        return [
            'title'     => __('notifications.message_sent.title', [
                'sender' => $senderName,
            ]),
            'message'   => str($this->message->body ?? '')->limit(60)->toString(),
            'object_id' => $this->message->conversation_id,
        ];
    }
}
