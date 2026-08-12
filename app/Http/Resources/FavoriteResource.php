<?php

namespace App\Http\Resources;

use App\Http\Resources\Service\ServiceIndexResource;
use App\Models\Service;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->favoritable;

        if (!$item) {
            return [
                'id' => $this->id,
                'type' => null,
                'item' => null,
            ];
        }

       
        $item->setAttribute('is_favorite', true);

        if ($this->favoritable_type === Service::class) {
            return [
                'id' => $this->id,
                'type' => 'service',
                'item' => new ServiceIndexResource($item),
            ];
        }

        if ($this->favoritable_type === ServiceProvider::class) {
            return [
                'id' => $this->id,
                'type' => 'service_provider',
                'item' => new ServiceProviderResource($item),
            ];
        }

        return [
            'id' => $this->id,
            'type' => null,
            'item' => null,
        ];
    }
}