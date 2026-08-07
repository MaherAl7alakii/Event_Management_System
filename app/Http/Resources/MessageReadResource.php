<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MessageReadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'marked_read' => $this['marked_read'],
            'read_at'     => $this['read_at'],
        ];
    }
}
