<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id'       => $this->sender_id,
            'is_mine'         => $this->sender_id === $request->user()->id,
            'type'            => $this->type,
            'body'            => $this->body,
            'media_url'       => $this->media_url,
            'is_edited'       => $this->is_edited,
            'edited_at'       => $this->edited_at?->format('Y-m-d H:i'),
            'is_read'         => $this->isRead(),
            'read_at'         => $this->read_at?->format('Y-m-d H:i'),
            'time'            => $this->created_at?->format('H:i'),
            'date'            => $this->created_at?->format('Y-m-d'),
            'sender' => [
                'id'     => $this->sender->id,
                'name'   => $this->sender->name,
                'avatar'    => $this->sender->hasRole('customer')
                    ? $this->sender->profile?->avatar
                    : $this->sender->serviceProvider?->avatar,
            ],
        ];
    }
}
