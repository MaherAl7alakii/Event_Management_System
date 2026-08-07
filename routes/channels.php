<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;




Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $conversation->hasParticipant($user->id);
});


Broadcast::channel('online-users', function ($user) {
    return [
        'id'     => $user->id,
        'name'   => $user->name,
        'avatar' => $user->avatar_url ?? null,
    ];
});
