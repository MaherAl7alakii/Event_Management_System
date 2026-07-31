<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'title' => $this->title,

            'type' => $this->type,

            'url' => asset('storage/' . $this->path),

            'category' => $this->category?->translate(app()->getLocale())?->name,

            'created_at' => $this->created_at,
        ];
    }
}