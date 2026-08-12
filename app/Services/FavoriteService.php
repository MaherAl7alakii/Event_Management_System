<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Service;
use App\Models\ServiceProvider;

class FavoriteService
{
   
    public function addFavorite(int $userId, string $type, int $id): array
    {
        $model = $this->resolveFavoritable($type, $id);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $userId,
            'favoritable_type' => $model::class,
            'favoritable_id' => $model->id,
        ]);

        return [
            'favorite' => $favorite,
            'created' => $favorite->wasRecentlyCreated,
        ];
    }

   
    public function removeFavorite(int $userId, string $type, int $id): bool
    {
        $model = $this->resolveFavoritable($type, $id);

        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', $model::class)
            ->where('favoritable_id', $model->id)
            ->delete() > 0;
    }

   
    public function getFavorites(int $userId)
    {
        return Favorite::where('user_id', $userId)
            ->with([
                'favoritable' => function ($morphTo) {

                    $morphTo->morphWith([

                        Service::class => [
                            'provider.serviceProvider',
                            'category',
                            'city.governorate',
                            'images',
                            'offer',
                        ],

                        ServiceProvider::class => [
                            'user',
                            'city.governorate',
                            'workingHours',
                            'categories',
                            'documents',
                            'portfolios',
                        ],

                    ]);
                },
            ])
            ->latest()
            ->get();
    }

    
    public function isFavorite(int $userId, $model): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', $model::class)
            ->where('favoritable_id', $model->id)
            ->exists();
    }

    
    public function getFavoriteServices(int $userId)
    {
        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', Service::class)
            ->with([
                'favoritable' => function ($query) {
                    $query->with([
                        'provider.serviceProvider',
                        'category',
                        'city.governorate',
                        'images',
                        'offer',
                    ]);
                },
            ])
            ->latest()
            ->get();
    }

   
    public function getFavoriteProviders(int $userId)
    {
        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', ServiceProvider::class)
            ->with([
                'favoritable' => function ($query) {
                    $query->with([
                        'user',
                        'city.governorate',
                        'workingHours',
                        'categories',
                        'documents',
                        'portfolios',
                    ]);
                },
            ])
            ->latest()
            ->get();
    }

    
    private function resolveFavoritable(string $type, int $id)
    {
        return match ($type) {

            'service' => Service::findOrFail($id),

            'service_provider' => ServiceProvider::findOrFail($id),

            default => throw new \InvalidArgumentException(
                'Invalid favorite type.'
            ),
        };
    }
}