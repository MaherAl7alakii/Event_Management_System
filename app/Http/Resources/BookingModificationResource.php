<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingModificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                          => $this->id,
            'booking_id'                  => $this->booking_id,
            'requested_by'                => $this->whenLoaded('requestedBy', function () {
                return [
                    'id'   => $this->requestedBy?->id,
                    'name' => $this->requestedBy?->name,
                ];
            }, [
                'id'   => $this->requestedBy?->id,
                'name' => $this->requestedBy?->name,
            ]),
            'old_values'                  => $this->old_values,
            'new_values'                  => $this->new_values,
//            'price_changed'               => (bool) $this->price_changed,
            'old_price'                   => $this->old_price !== null ? number_format((float) $this->old_price, 2, '.', '') : null,
            'new_price'                   => $this->new_price !== null ? number_format((float) $this->new_price, 2, '.', '') : null,
            'status'                      => $this->status,
            'requires_customer_approval'  => (bool) $this->requires_customer_approval,
            'requires_provider_approval'  => (bool) $this->requires_provider_approval,
            'customer_approved'          => $this->customer_approved !== null ? (bool) $this->customer_approved : null,
            'customer_responded_at'       => $this->customer_responded_at?->format('Y-m-d H:i'),
            'provider_approved'          => $this->provider_approved !== null ? (bool) $this->provider_approved : null,
            'provider_responded_at'       => $this->provider_responded_at?->format('Y-m-d H:i'),
//            'respond_by'                  => $this->respond_by,
            'created_at'                  => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
