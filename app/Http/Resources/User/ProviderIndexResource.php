<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $provider = $this->serviceProvider;
        return [
            'id' => $this->id,
            'service_provider_id' => $provider->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
            'business_name'       => $this->serviceProvider->business_name,
            'avatar'              => $provider?->avatar,
            'phone'               => $provider?->phone,
            'address'             => $provider?->address,
            'approval_status'     => $provider?->approval_status,
            'years_of_experience' => $provider?->years_of_experience,
            'governorate' =>[
                'id' =>$provider->city->governorate->id,
                'name' =>$provider->city->governorate->name,
            ],
            'city' =>[
                'id' =>$provider->city->id,
                'name' =>$provider->city->name,
            ],
            'verified_at'         => $provider->verified_at?->format('Y-m-d H:i'),
            'created_at'         =>  $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
