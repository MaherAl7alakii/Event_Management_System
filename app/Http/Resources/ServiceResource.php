<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = auth('api')->user();
        $isProvider = $user && $user->hasRole('service_provider');
$offer = $this->offer;

if ($offer &&(!$offer->is_active ||today()->gt($offer->end_date))) {
    $offer = null;
}
        if ($request->routeIs('services.index')) {
            $primaryImage = $this->images->where('is_primary', true)->first() ?? $this->images->first();

            return [
                'id' => $this->id,
                'title' => $this->title,
                'provider_name' => $this->provider->name,
                'is_favorite' => (bool) ($this->is_favorite ?? false),
                'base_price' => $this->base_price,
                'rating' => (float)$this->rating,
                'rating_count' => $this->rating_count,
                'bookings_count' => $this->bookings_count,
                'category_id' => $this->category_id,
                'category_name' => $this->category->name,
                'category_icon' => $this->category->icon,
                'governorate_id'   => $this->city?->governorate_id,
                'governorate_name' => $this->city?->governorate?->name,
                'city_id' => $this->city_id,
                'city_name' => $this->city?->name,
                'offer' => $offer ? [
    'id' => $offer->id,
    'discount' => $offer->discount,
    'offer_price' => $offer->offer_price,
    'original_price' => $offer->original_price,
] : null,
                'image' => $primaryImage?->url
            ];
        }


        $fullDetails = [
            'id' => $this->id,
            'provider_name' => $this->provider->name,
            'is_favorite' => (bool) ($this->is_favorite ?? false),
            'pricing_type' => $this->pricing_type->value,
            'pricing_type_view' => $this->pricing_type->view(),
            'base_price' => $this->base_price,
            'min_hours' => $this->min_hours,
            'max_hours' => $this->max_hours,
            'max_guests' => $this->max_guests,
            'rating' => (float)$this->rating,
            'rating_count' => $this->rating_count,
            'bookings_count' => $this->bookings_count,
            'reviews_count' => $this->reviews_count,
            'category_id' => $this->category_id,
            'category_name' => $this->category->name,
            'category_icon' => $this->category->icon,
            'governorate_id'   => $this->city?->governorate_id,
            'governorate_name' => $this->city?->governorate?->name,
            'city_id' => $this->city_id,
            'city_name' => $this->city?->name,

            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary,
                ];
            }),
        ];




        if ($isProvider) {
            $fullDetails['is_active'] = $this->is_active;

            $fullDetails['features'] = $this->whenLoaded('features', function () {
                return $this->features->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'ar' => [
                            'value' => $feature->getTranslatedValue('ar') ?? null,
                        ],
                        'en' => [
                            'value' => $feature->getTranslatedValue('en') ?? null,
                        ],
                    ];
                });
            });


            $translations = $this->translations->mapWithKeys(function ($translation) {
                return [
                    $translation->locale => [
                        'title' => $translation->title,
                        'description' => $translation->description,
                        'address' => $translation->address,
                    ]
                ];
            })->toArray();

            return array_merge($fullDetails, [

                'ar' => $translations['ar'] ?? [
                        'title' => null,
                        'description' => null,
                        'address' => null,
                    ],
                'en' => $translations['en'] ?? [
                        'title' => null,
                        'description' => null,
                        'address' => null,
                    ],
            ]);
        }


        $fullDetails['features'] = $this->whenLoaded('features', function () {
            return $this->features->map(function ($feature) {
                return [
                    'id'    => $feature->id,
                    'value' => $feature->value,
                ];
            }) ;
        });


        return array_merge($fullDetails, [
            'title' => $this->title,
            'description' => $this->description,
            'address' => $this->address,
        ]);
    }
}
