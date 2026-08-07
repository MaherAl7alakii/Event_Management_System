<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConversationService
{

    public function getAllConversations(User $user)
    {
        return Conversation::query()
            ->where(function ($q) use ($user) {
                $q->where('user_one_id', $user->id)
                  ->orWhere('user_two_id', $user->id);
            })
            ->with(['userOne', 'userTwo', 'lastMessage'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->whereNull('read_at')
                  ->where('sender_id', '!=', $user->id);
            }])
            ->whereNotNull('last_message_at')
            ->orderByDesc('last_message_at')->get();
    }


    public function findOrCreateConversation(User $user, int $otherUserId): Conversation
    {
        if ($user->id === $otherUserId) {
            throw new \InvalidArgumentException('cannot_message_self');
        }

        $conversation = Conversation::findOrCreateBetween($user->id, $otherUserId);
        $conversation->load(['userOne', 'userTwo', 'lastMessage']);

        return $conversation;
    }


    public function getConversationById(Conversation $conversation): Conversation
    {
        return $conversation->load(['userOne', 'userTwo', 'lastMessage']);
    }
}
