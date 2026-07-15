<?php

namespace App\Http\Resources\Booking;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryImage = $this->service->images->where('is_primary', true)?->first() ?? $this->service->images?->first();
        return [
            'id'              => $this->id,
            'service'         => [
                'id'    => $this->service_id,
                'name'  => $this->service->title,
                'image' => $primaryImage?->url,
                'category' => [
                    'id'   => $this->service->category_id,
                    'name' => $this->service->category->name,
                ]
            ],
            'provider' => [
                'id'   => $this->provider->serviceProvider?->id,
                'name' => $this->provider->serviceProvider?->business_name,
            ],
            'governorate' => [
                'id'   => $this->event->city->governorate->id,
                'name' => $this->event->city->governorate->name,
            ],
            'city' => [
                'id'   => $this->event->city->id,
                'name' => $this->event->city->name,
            ],
            'service_date'    => $this->service_date->format('Y-m-d'),
            'start_time'      => $this->start_time->format('H:i'),
            'status'          => $this->status,
            'estimated_price' => (float) $this->estimated_price,
        ];
    }
}
