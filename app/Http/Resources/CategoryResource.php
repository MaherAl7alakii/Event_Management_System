<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdmin = auth('api')->user()?->hasRole('admin');

        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'is_active' => $this->is_active,

            $this->mergeWhen($isAdmin, [
                'en' => [
                    'name' => $this->translations->where('locale', 'en')->first()?->name
                ],
                'ar' => [
                    'name' => $this->translations->where('locale', 'ar')->first()?->name
                ],
            ]),


            $this->mergeWhen(!$isAdmin, [
                'name' => $this->name
            ]),
        ];
    }
}
