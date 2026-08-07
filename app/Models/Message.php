<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'type',
        'body',
        'media_url',
        'media_meta',
        'is_edited',
        'edited_at',
        'read_at',
    ];

    protected $casts = [
        'media_meta' => 'array',
        'is_edited'  => 'boolean',
        'edited_at'  => 'datetime',
        'read_at'    => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }


    public function toBroadcastArray(): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id'       => $this->sender_id,
            'type'            => $this->type,
            'body'            => $this->body,
            'media_url'       => $this->media_url,
            'media_meta'      => $this->media_meta,
            'is_edited'       => $this->is_edited,
            'edited_at'       => $this->edited_at?->format("Y-m-d H:i"),
            'read_at'         => $this->read_at?->format("Y-m-d H:i"),
            'created_at'      => $this->created_at?->format("Y-m-d H:i"),
            'sender'          => [
                'id'     => $this->sender->id,
                'name'   => $this->sender->name,
                'avatar'    => $this->sender->hasRole('customer')
                    ? $this->sender->profile?->avatar
                    : $this->sender->serviceProvider?->avatar,
            ],
        ];
    }
}
