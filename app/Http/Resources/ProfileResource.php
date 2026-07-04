<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'address' => $this->address,
            'gender' => $this->gender,
            'birth_of_date' => $this->birth_of_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}