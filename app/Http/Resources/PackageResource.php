<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PackageResource extends JsonResource
{


    public function toArray(Request $request): array
    {


        return [

            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'image'=>$this->image?asset('storage/'.$this->image):null,
            'total_price'=>(float)$this->total_price,
            'discount_percentage' => (float)$this->discount,
            'discount_amount' => round($this->total_price * $this->discount / 100,2),
            'final_price'=>(float)$this->final_price,
            'services_count' => $this->services_count ?? $this->services->count(),
            'status'=>$this->status,
            'provider'=>new ServiceProviderResource($this->whenLoaded('provider')),
            'services'=>ServiceResource::collection($this->whenLoaded('services')),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];

    }

}