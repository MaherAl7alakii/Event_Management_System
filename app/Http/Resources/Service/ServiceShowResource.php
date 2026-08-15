<?php

namespace App\Http\Resources\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('api')->user();
        $offer = $this->offer;

if ($offer &&(!$offer->is_active ||today()->gt($offer->end_date))) {
    $offer = null;
}
        $isProvider = $user && $user->hasRole('service_provider') && $user->id === $this->provider_id;
        $translations = $this->translations->mapWithKeys(function ($translation) {
            return [
                $translation->locale => [
                    'title'       => $translation->title,
                    'description' => $translation->description,
                    'address'     => $translation->address,
                ]
            ];
        })->toArray();

        return [
            'id'       => $this->id,
            'provider' => [
                'id' => $this->provider->serviceProvider->id,
                'name' => $this->provider->serviceProvider->business_name,
                'avatar' => $this->provider->serviceProvider->avatar,
            ],
            'is_favorite' =>(bool) ($this->is_favorite ?? false),
            'pricing_type'      => $this->pricing_type->value,
            'pricing_type_view' => $this->pricing_type->view(),
            'base_price'        => $this->base_price,
            'min_hours'         => $this->min_hours,
            'max_hours'         => $this->max_hours,
            'max_guests'        => $this->max_guests,
            'rating'            => (float) $this->rating,
            'rating_count'      => $this->rating_count,
            'bookings_count'    => $this->bookings_count,
            'reviews_count'     => $this->reviews_count,
            'category_id'       => $this->category_id,
            'category_name'     => $this->category->name,
            'category_icon'     => $this->category->icon,
            'governorate_id'    => $this->city?->governorate_id,
            'governorate_name'  => $this->city?->governorate?->name,
            'city_id'           => $this->city_id,
            'city_name'         => $this->city?->name,


            'is_active'         => $this->when($isProvider, $this->is_active),

            'title'             => $this->when(! $isProvider, $this->title),
            'description'       => $this->when(! $isProvider, $this->description),
            'address'           => $this->when(! $isProvider, $this->address),
'offer' => $offer ? [
    'id' => $offer->id,
    'discount' => $offer->discount,
    'original_price' => $offer->original_price,
    'offer_price' => $offer->offer_price,
    'start_date' => $offer->start_date,
    'end_date' => $offer->end_date,
] : null,

            'images' => $this->images->map(function ($image) {
                return [
                    'id'         => $image->id,
                    'url'        => $image->url,
                    'is_primary' => $image->is_primary,
                ];
            }),

            'features' => $this->whenLoaded('features', function () use ($isProvider) {
                return $this->features->map(function ($feature) use ($isProvider) {
                    if ($isProvider) {
                        return [
                            'id' => $feature->id,
                            'ar' => ['value' => $feature->getTranslatedValue('ar') ?? null],
                            'en' => ['value' => $feature->getTranslatedValue('en') ?? null],
                        ];
                    }
                    return [
                        'id'    => $feature->id,
                        'value' => $feature->value,
                    ];
                });
            }),

            $this->mergeWhen($isProvider, [
                'ar' => $translations['ar'] ?? [
                        'title'       => null,
                        'description' => null,
                        'address'     => null,
                    ],
                'en' => $translations['en'] ?? [
                        'title'       => null,
                        'description' => null,
                        'address'     => null,
                    ],
            ]),
        ];
    }
}
