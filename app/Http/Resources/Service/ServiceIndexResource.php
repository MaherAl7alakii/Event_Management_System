<?php

namespace App\Http\Resources\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryImage = $this->images->where('is_primary', true)->first() ?? $this->images->first();
$offer = $this->offer;

if ($offer &&(!$offer->is_active ||today()->gt($offer->end_date))) {
    $offer = null;
}
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'provider_name'    => $this->provider->serviceProvider->business_name,
             'is_favorite' =>(bool) ($this->is_favorite ?? false),
            'base_price'       => $this->base_price,
            'rating'           => (float) $this->rating,
            'rating_count'     => $this->rating_count,
            'bookings_count'   => $this->bookings_count,
            'category_id'      => $this->category_id,
            'category_name'    => $this->category->name,
            'category_icon'    => $this->category->icon,
            'governorate_id'   => $this->city?->governorate_id,
            'governorate_name' => $this->city?->governorate?->name,
            'city_id'          => $this->city_id,
            'city_name'        => $this->city?->name,
            'image'            => $primaryImage?->url,
  'offer' => $offer ? [
    'id' => $offer->id,
    'discount' => $offer->discount,
    'offer_price' => $offer->offer_price,
    'original_price' => $offer->original_price,
] : null,
        ];
    }
}
