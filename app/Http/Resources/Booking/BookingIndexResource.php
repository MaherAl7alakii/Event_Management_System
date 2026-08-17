<?php

namespace App\Http\Resources\Booking;

use Carbon\Carbon;
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
        $originalPrice = $this->original_price;


        $estimatedPrice = (float) $this->estimated_price;

        $additionalCosts = $this->final_price !== null
            ? max(0, (float) $this->final_price - $estimatedPrice)
            : 0.0;

        $finalPriceBeforeDiscount = $originalPrice + $additionalCosts;
        $finalPriceAfterDiscount  = $estimatedPrice + $additionalCosts;

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
            'customer' => [
                'id'   => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'governorate' => [
                'id'   => $this->event->city->governorate->id,
                'name' => $this->event->city->governorate->name,
            ],
            'city' => [
                'id'   => $this->event->city->id,
                'name' => $this->event->city->name,
            ],
            'package_id' => $this->package_id,
            'booking_date'    => $this->booking_date->format('Y-m-d'),
            'start_time'      => $this->start_time->format('H:i'),
            'status'          => $this->status->value,
            'original_price'  => $this->original_price,
            'estimated_price'        => (float) $this->estimated_price,
            'additional_costs'           => $additionalCosts,


            'original_final_price'=> $finalPriceBeforeDiscount,
            'final_price'       => $this->final_price ==null ? (float) $this->estimated_price : (float) $this->final_price,
            'deposit_deadline'       => $this->formatDeadline($this->deposit_deadline_at),
            'final_payment_deadline' => $this->formatDeadline($this->final_payment_deadline_at),
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
