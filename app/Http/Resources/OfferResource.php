<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'discount' => $this->discount,

            'original_price' => $this->original_price,

            'offer_price' => $this->offer_price,

            'start_date' => $this->start_date->format('Y-m-d'),

            'end_date' => $this->end_date->format('Y-m-d'),

            'is_active' => $this->is_active,

            
        ];
    }
}