<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\User;


abstract class AppNotification
{
    abstract public function type(): NotificationType;


    abstract public function recipients(): array;


    abstract public function messageFor(User $recipient): array;
}
