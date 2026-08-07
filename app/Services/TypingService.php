<?php

namespace App\Services;

use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\User;

class TypingService
{

    public function broadcastTyping(Conversation $conversation, User $user, bool $isTyping): void
    {
        broadcast(new UserTyping(
            $conversation->id,
            $user->id,
            $isTyping
        ))->toOthers();
    }
}
