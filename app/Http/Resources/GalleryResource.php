<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
         $url = filter_var($this->path, FILTER_VALIDATE_URL)
            ? $this->path
            : asset('storage/' . $this->path);
        return [

            'id' => $this->id,

            'title' => $this->title,

            'type' => $this->type,

            'url' => $url,

            'category' => $this->category?->translate(app()->getLocale())?->name,

            'created_at' => $this->created_at,
        ];
    }
}