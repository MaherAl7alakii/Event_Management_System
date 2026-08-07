<?php

namespace App\Services;

use App\Events\MessageDeleted;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessageService
{

    public function getMessages(Conversation $conversation)
    {
        return $conversation->messages()
            ->with('sender')
            ->orderByDesc('created_at')->get();
    }


    public function sendMessage(Conversation $conversation, User $sender, array $data): Message
    {
        $message = DB::transaction(function () use ($conversation, $sender, $data) {
            $message = $conversation->messages()->create([
                'sender_id'  => $sender->id,
                'type'       => $data['type'],
                'body'       => $data['body'] ?? null,
                'media_url'  => $data['media_url'] ?? null,
            ]);

            $conversation->update([
                'last_message_id' => $message->id,
                'last_message_at' => $message->created_at,
            ]);

            return $message;
        });

        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }


    public function updateMessage(Message $message, string $body): Message
    {
        $message->update([
            'body'      => $body,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        $message->load('sender');

        broadcast(new MessageUpdated($message))->toOthers();

        return $message;
    }


    public function deleteMessage(Conversation $conversation, Message $message): void
    {
        $message->delete();

        if ($conversation->last_message_id === $message->id) {
            $latest = $conversation->messages()->orderByDesc('created_at')->first();

            $conversation->update([
                'last_message_id' => $latest?->id,
                'last_message_at' => $latest?->created_at,
            ]);
        }

        broadcast(new MessageDeleted($message->id, $conversation->id))->toOthers();
    }


    public function markAsRead(Conversation $conversation, User $reader): array
    {
        $readAt = now();

        $unreadIds = $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $reader->id)
            ->pluck('id');

        if ($unreadIds->isNotEmpty()) {
            $conversation->messages()
                ->whereIn('id', $unreadIds)
                ->update(['read_at' => $readAt]);

            broadcast(new MessageRead(
                $conversation->id,
                $unreadIds->toArray(),
                $reader->id,
                $readAt->format('Y-m-d H:i')
            ))->toOthers();
        }

        return [
            'marked_read' => $unreadIds->values()->toArray(),
            'read_at'     => $readAt->format('Y-m-d H:i'),
        ];
    }
}
