<?php

namespace App\Http\Resources;

use App\Http\Resources\Booking\BookingIndexResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingPriceProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'booking_id'  => $this->booking_id,
            'old_price'   => (float) $this->old_price,
            'new_price'   => (float) $this->new_price,

            'status'      => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,

            'respond_by'  => $this->respond_by ? $this->respond_by->format('Y-m-d H:i:s') : null,
            'created_at'  => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,

            'booking'     => new BookingIndexResource($this->whenLoaded('booking')),
        ];
    }
}
