<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderPayoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'amount'     => (float) $this->amount,
            'reason'     => $this->reason->value,
            'status'     => $this->status->value,
            'release_at' => $this->whenNotNull($this->release_at?->format('Y-m-d H:i:s')),
            'booking' => $this->whenLoaded('booking', fn () => [
                'id'      => $this->booking->id,
                'service' => $this->booking->service?->title,
            ]),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
