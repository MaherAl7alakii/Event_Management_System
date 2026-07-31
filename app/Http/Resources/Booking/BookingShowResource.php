<?php

namespace App\Http\Resources\Booking;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingShowResource extends JsonResource
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
            'id'                => $this->id,
            'status'            => $this->status->value,
            'booking_date'      => $this->booking_date->format('Y-m-d'),
            'start_time'        => $this->start_time->format('H:i'),
            'duration'          => $this->whenNotNull($this->duration),
            'end_time'          => $this->endsAt()->format('H:i'),
            'quantity'          => $this->whenNotNull($this->quantity),
            'customer_notes'    => $this->customer_notes,
            'estimated_price'   => (float) $this->estimated_price,
            'final_price'       => (float) $this->final_price,

            'buffer_after_minutes'   => $this->buffer_after_minutes,
            'deposit_deadline'       => $this->formatDeadline($this->deposit_deadline_at),
            'final_payment_deadline' => $this->formatDeadline($this->final_payment_deadline_at),

            'service' => [
                'id'                => $this->service_id,
                'title'             => $this->service->title,
                'category'    => [
                    'id' => $this->service->category->id,
                    'name' => $this->service->category->name,
                ],
                'image'             => $primaryImage?->url,
                'pricing_type'      => $this->pricing_type->value,
                'pricing_type_view' => $this->pricing_type->view(),
                'base_price'        => $this->base_price,
            ],

            'governorate' => [
                'id'   => $this->event->city->governorate->id,
                'name' => $this->event->city->governorate->name,
            ],
            'city' => [
                'id'   => $this->event->city->id,
                'name' => $this->event->city->name,
            ],


            'provider' => [
                'id'   => $this->provider->serviceProvider->id,
                'name' => $this->provider->serviceProvider->business_name,
            ],


            'customer' => [
                'id'   => $this->customer->id,
                'name' => $this->customer->name,
            ],

            'submitted_at' => $this->whenNotNull($this->submitted_at?->format('Y-m-d H:i:s')),
            'accepted_at'  => $this->whenNotNull($this->accepted_at?->format('Y-m-d H:i:s')),
            'rejected_at'  => $this->whenNotNull($this->rejected_at?->format('Y-m-d H:i:s')),
            'confirmed_at' => $this->whenNotNull($this->confirmed_at?->format('Y-m-d H:i:s')),
            'completed_at' => $this->whenNotNull($this->completed_at?->format('Y-m-d H:i:s')),
        ];
    }

    private function formatDeadline(?Carbon $deadline): ?array
    {
        if (! $deadline || $deadline->isPast()) {
            return null;
        }

        $now = now();
        $remainingHours = $now->diffInHours($deadline);


        if ($remainingHours > 72) {
            return [
                'type'      => 'datetime',
                'value' => $deadline->format('Y-m-d H:i'),
            ];
        }

        $diff = $now->diff($deadline);

        return [
            'type'              => 'timer',
            'remaining_seconds' =>(int) $now->diffInSeconds($deadline),
            'value'         => sprintf('%02d:%02d:%02d', ($diff->days * 24) + $diff->h, $diff->i, $diff->s),
        ];
    }
}
