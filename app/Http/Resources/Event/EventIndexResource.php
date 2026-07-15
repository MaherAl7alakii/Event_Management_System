<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'cover_image'  => $this->cover_image,
            'event_date'   => $this->event_date->format('Y-m-d'),
            'status'       => $this->status,
            'governorate_name' => $this->city?->governorate?->name,
            'city_name'    => $this->city?->name,
            'service_count' => 5
        ];
    }
}
