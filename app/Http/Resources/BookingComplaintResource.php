<?php

namespace App\Http\Resources;

use App\Http\Resources\Booking\BookingIndexResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'customer_id'   => $this->customer_id,
            'provider_id' => $this->booking?->provider?->serviceProvider->id,
            'customer_name' => $this->customer?->name,
            'provider_name' => $this->booking?->provider?->serviceProvider->business_name,
            'status'        => $this->status,

            'description' => $this->when($this->relationLoaded('booking'), $this->description),
            'booking'       => new BookingIndexResource($this->whenLoaded('booking')),

            'created_at'    => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
