<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Throwable;

class NotificationService
{

    public function __Construct(protected Messaging $messaging)
    {
    }

    public function getNotifications($user)
    {
        return $user->notifications;
    }

    public function readWithSubject(Notification $notification)
    {
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $type = NotificationType::from($notification->type);
        $objectId = $notification->data['object_id'] ?? null;

        return [
            'type'       => $type->value,
            'subject'    => $this->resolveSubject($type, $objectId),
        ];
    }


    protected function resolveSubject(NotificationType $type, mixed $objectId): mixed
    {
        $modelClass = $type->subjectModel();

        if ($modelClass === null || $objectId === null) {
            return null;
        }

        $model = $modelClass::find($objectId);

        if ($model === null) {
            return null;
        }

        $resourceClass = $type->subjectResource();

        return $resourceClass !== null ? new $resourceClass($model) : $model;
    }

    public function send(
        User   $user,
        string $type,
        string $title,
        string $message,
        mixed  $objectId = null
    ): bool
    {
        try {

            $this->storeInDatabase($user, $type, $title, $message, $objectId);

            $this->sendFcmNotification($user, $title, $message);

            return true;
        } catch (Throwable $e) {
            Log::error('Notification Delivery Failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'type' => $type,
            ]);
            return false;
        }
    }

    protected function storeInDatabase(User $user, string $type, string $title, string $message, mixed $objectId): void
    {
        $user->notifications()->create([
            'type' => $type,
            'data' => [
                'user' => $user->name,
                'title' => $title,
                'message' => $message,
                'object_id' => $objectId,
            ],
        ]);
    }


    protected function sendFcmNotification(User $user, string $title, string $message): void
    {
        $fcmTokens = $user->fcmTokens()->pluck('fcm_token')->filter()->toArray();

        if (empty($fcmTokens)) {
            return;
        }

        $unreadCount = $user->unreadNotifications()->count();

        $cloudMessage = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($title, $message))
            ->withData([
                'title' => $title,
                'message' => $message,
                'notification_count' => (string)$unreadCount,
            ]);

        $this->messaging->sendMulticast($cloudMessage, $fcmTokens);
    }


    public function markAllAsRead(User $user)
    {
        $user->unreadNotifications->markAsRead();
    }


    public function deleteNotification(Notification $notification)
    {
        $notification->delete();
    }


    public function deleteAllNotifications(User $user)
    {
        $user->notifications()->delete();
    }

}



