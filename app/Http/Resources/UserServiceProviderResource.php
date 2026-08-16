<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserServiceProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
              'name' => $this->user?->name,
              'email' => $this->user?->email,
              'is_banned' => (bool) ($this->user?->is_banned ?? false),
                    ],
            'phone' => $this->phone,
            'approval_status' => $this->approval_status,
            'rejection_reason' => $this->rejection_reason,
            'business_name' => $this->business_name,
            'is_favorite' => (bool) ($this->is_favorite ?? false),
            'avatar' => $this->avatar
                 ? (
                    str_starts_with($this->avatar, 'http')
                        ? $this->avatar
                        : asset('storage/' . $this->avatar)
                )
                : null,
            'years_of_experience' => $this->years_of_experience,
            'rating' => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,
            'description' => $this->description,
            'city' => new CityResource($this->whenLoaded('city')),
            'governorate' => $this->when(
                $this->relationLoaded('city') &&
                $this->city?->governorate,
                fn () => new GovernorateResource(
                    $this->city->governorate
                )
            ),
            'address' => $this->address,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'working_hours' => WorkingHourResource::collection(
                $this->whenLoaded('workingHours')
            ),
        ];
    }
}