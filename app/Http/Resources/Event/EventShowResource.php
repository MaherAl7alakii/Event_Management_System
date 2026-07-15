<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'cover_image'        => $this->cover_image,
            'governorate_id'     => $this->city?->governorate_id,
            'governorate_name'   => $this->city?->governorate?->name,
            'city_id'            => $this->city_id,
            'city_name'          => $this->city?->name,
            'event_date'         => $this->event_date->format('Y-m-d'),
            'start_time'         => $this->start_time,
            'end_time'           => $this->end_time,
            'event_type_id'      =>$this->eventType->id,
            'event_type_name' => $this->other_type ?? $this->eventType?->name,
            'guests_count'       => (int) $this->guests_count,
            'status'             => $this->status,
            'submitted_at'       => $this->submitted_at?->format('Y-m-d H:i:s'),
            'confirmed_at'       => $this->confirmed_at?->format('Y-m-d H:i:s'),
        ];
    }
}
