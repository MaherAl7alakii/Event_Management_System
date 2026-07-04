<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceProviderResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'city' => new CityResource($this->whenLoaded('city')),
            'account_type' => $this->account_type,
            'business_name' => $this->business_name,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'phone' => $this->phone,
            'address' => $this->address,
            'approval_status' => $this->approval_status,
            'years_of_experience' => $this->years_of_experience,
            'description' => $this->description,
            'verified_at' => $this->verified_at,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}