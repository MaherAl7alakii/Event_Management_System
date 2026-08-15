<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'payment_type' => $this->payment_type->value,
            'status'       => $this->status->value,
            'amount'       => (float) $this->amount,
            'event' => $this->whenLoaded('event', fn () => [
                'id'    => $this->event->id,
                'title' => $this->event->title,
            ]),
//            'booking' => $this->whenLoaded('booking', fn () => $this->booking ? [
//                'id'      => $this->booking->id,
//                'service' => $this->booking->service?->title,
//            ] : null),
            'refunds' => $this->whenLoaded('refunds', fn () => $this->refunds->map(fn ($refund) => [
                'id'     => $refund->id,
                'amount' => (float) $refund->amount,
                'status' => $refund->status->value,
                'reason' => $refund->reason,
                'created_at' => $refund->created_at->format('Y-m-d H:i:s'),
            ])),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
