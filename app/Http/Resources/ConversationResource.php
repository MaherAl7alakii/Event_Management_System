<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $authId = $request->user()->id;
        $otherUser = $this->otherUser($authId);

        return [
            'id' => $this->id,
            'other_user' => [
                'id'            => $otherUser->id,
                'name'          => $otherUser->name,
                'avatar'    => $otherUser->hasRole('customer')
                    ? $otherUser->profile?->avatar
                    : $otherUser->serviceProvider?->avatar,
                'last_seen_at'  => $otherUser->last_seen_at?->format("Y-m-d H:i"),
            ],
            'last_message' => $this->whenLoaded('lastMessage', function () {
                return [
                    'id'         => $this->lastMessage->id,
                    'type'       => $this->lastMessage->type,
                    'preview'    => $this->buildPreview($this->lastMessage),
                    'sender_id'  => $this->lastMessage->sender_id,
                    'is_read'    => $this->lastMessage->isRead(),
                    'time'       => $this->lastMessage->created_at?->format('H:i'),
                    'date'       => $this->lastMessage->created_at?->format('Y-m-d'),
                ];
            }),
            'unread_count' => $this->when(
                isset($this->unread_count),
                fn () => $this->unread_count
            ),
        ];
    }

    private function buildPreview($message): string
    {
        return match ($message->type) {
            'image' => '📷 image',
            'video' => '🎥 video',
            'file' => ' 📄 file',
            default => str($message->body ?? '')->limit(60)->toString(),
        };
    }
}
