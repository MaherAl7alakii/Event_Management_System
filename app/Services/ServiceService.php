<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceTranslation;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;

class ServiceService
{
    public function __construct(
        private readonly ServiceLinkService $serviceLinks,
    ) {
    }

    public function getServices($request)
    {
        $user = auth('api')->user();

        $services = Service::with(['category', 'city.governorate', 'images','offer'])
         ->when(
                auth('api')->check(),
                function ($query) {
                    $query->withExists([
                        'favorites as is_favorite' => function ($q) {
                            $q->where('user_id', auth('api')->id());
                        },
                    ]);
                }
            )
            ->when($user?->hasRole('admin'), function ($query) {
                return $query;
            }, function ($query) use ($user) {
                return $query->when($user?->hasRole('service_provider'), function ($q) use ($user) {
                    $q->where('provider_id', $user->id);
                }, function ($q) {
                    $q->where('is_active', true);
                });
            })
            ->filter($request->all())
            ->sort($request->query('sort_by'))
            ->paginate($request->query('per_page', 15))
            ->onEachSide(2);

        $searchKeyword = $request->query('search_key');

        if ($searchKeyword && $user && $services->total() > 0) {
            $user->searchHistory()->updateOrCreate(
                ['keyword' => $searchKeyword],
                ['updated_at' => now()]
            );
        }

        return $services;
    }


    public function getService(Service $service)
    {

         $service->load([
            'features',
            'offer',
            'category',
            'city.governorate',
            'images',
            'provider.serviceProvider',
        ]);

        $user = auth('api')->user();

        $service->setAttribute(
        'is_favorite',
        $user
            ? $service->favorites()
                ->where('user_id', $user->id)
                ->exists()
            : false
    );


        return $service;
    }



    public function createService(array $data)
    {
        return DB::transaction(function () use ($data) {

            $imagesData = $data['images'] ?? [];
            $featuresData = $data['features'] ?? [];
            $linkedServiceIds = $data['linked_service_ids'] ?? [];

            unset($data['images'], $data['features']);

            $data['provider_id'] = auth()->id();

            $service = Service::create($data);

            if(!empty($imagesData)){
                $requestedPrimaryUrl = collect($imagesData)->where('is_primary', true)->first()['url'] ?? null;
                $newUrls = collect($imagesData)->pluck('url')->toArray();
                $this->createImages($service, $newUrls, $requestedPrimaryUrl);
            }


            if (!empty($featuresData)) {
                $this->createFeatures($service, $featuresData);
            }

            if (!empty($linkedServiceIds)) {
                $this->syncLinkedServices($service, $linkedServiceIds);
            }

            return $service->refresh()->load('features');
        });
    }

    public function updateService(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {


            $imagesData = $data['images'] ?? [];
            $featuresData = $data['features'] ?? [];
            $linkedServiceIds = $data['linked_service_ids'] ?? null;


            unset($data['images'], $data['features']);

            $supportedLocales = ['ar', 'en'];

            foreach ($supportedLocales as $locale) {
                if (isset($data[$locale])) {
                    $service->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $data[$locale]
                    );
                } else {
                    $service->translations()->where('locale', $locale)->delete();
                }
            }

            $service->update($data);


            if (isset($imagesData)) {
                $newUrls = collect($imagesData)->pluck('url')->toArray();
                $requestedPrimaryUrl = collect($imagesData)->where('is_primary', true)->first()['url'] ?? null;

                $this->syncImages($service, $newUrls, $requestedPrimaryUrl);
            }
            $service->features()->delete();

            if (isset($featuresData)) {
                $this->createFeatures($service, $featuresData);
            }

            if (!empty($linkedServiceIds)) {
                $this->syncLinkedServices($service, $linkedServiceIds);
            }


            return $service->refresh()->load('features');
        });
    }





    public function deleteService(Service $service)
    {
        $service->delete();

        return true;
    }



    public function getSearchSuggestions($request)
    {
        $searchKey = $request->query('search_key');

        if (!$searchKey) {
            return collect();
        }

        return ServiceTranslation::where(function ($query) use ($searchKey) {
            $query->where('title', 'LIKE', "%{$searchKey}%")
                ->orWhere('description', 'LIKE', "%{$searchKey}%");
        })
            ->whereHas('service', function ($query) {
                if (auth()->check() && auth()->user()->hasRole('provider')) {
                    $query->where('provider_id', auth()->id());
                } else {
                    $query->where('is_active', true);
                }
            })
            ->limit(20)
            ->pluck('title')
            ->unique()
            ->values();
    }


    private function createImages(Service $service, array $urls, ?string $requestedPrimaryUrl = null): void
    {
        if (empty($urls)) {
            return;
        }

        $insertData = [];
        foreach ($urls as $url) {
            $insertData[] = [
                'service_id' => $service->id,
                'url'        => $url,
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $service->images()->insert($insertData);


        $primaryImage = $service->images()->where('url', $requestedPrimaryUrl)->first()
            ?? $service->images()->first();

        if ($primaryImage) {
            $primaryImage->update(['is_primary' => true]);
        }
    }


    private function syncImages(Service $service, array $newImages): void
    {
        $existingImages = $service->images()->pluck('url')->toArray();


        $toDelete = array_diff($existingImages, $newImages);

        if (!empty($toDelete)) {
            $service->images()
                ->whereIn('url', $toDelete)
                ->delete();
        }


        $toAdd = array_diff($newImages, $existingImages);

        foreach ($toAdd as $url) {
            $service->images()->create([
                'url' => $url,
                'is_primary' => false,
            ]);
        }

        if (!$service->images()->where('is_primary', true)->exists()) {
            $first = $service->images()->first();

            if ($first) {
                $first->update(['is_primary' => true]);
            }
        }
    }


    private function createFeatures(Service $service, array $featuresData): void
    {
        foreach($featuresData as $feathure) {
            $service->features()->create($feathure);
        }
    }




    private function syncLinkedServices(Service $service, array $requestedLinkedServiceIds): void
    {
        $requestedIds = collect($requestedLinkedServiceIds)
            ->reject(fn ($id) => (int) $id === $service->id)
            ->unique()
            ->values();

        $currentIds = $this->serviceLinks->linkedServiceIdsFor($service->id);

        $toLink = $requestedIds->diff($currentIds);
        $toUnlink = $currentIds->diff($requestedIds);

        foreach ($toLink as $id) {
            $other = Service::find($id);

            if ($other) {
                $this->serviceLinks->link($service, $other);
            }
        }

        foreach ($toUnlink as $id) {
            $other = Service::find($id);

            if ($other) {
                $this->serviceLinks->unlink($service, $other);
            }
        }
    }

public function getProviderCategoryServices($request, $providerId, $categoryId)
{
    $provider = ServiceProvider::findOrFail($providerId);

    return Service::query()
        ->where('provider_id', $provider->user_id)
        ->where('category_id', $categoryId)
        ->where('is_active', true)
        ->whereHas('offer', function ($query) {
         $query->where('is_active', true)
        ->whereDate('end_date', '>=', today());
})
        ->when(
                auth('api')->check(),
                function ($query) {

                    $query->withExists([
                        'favorites as is_favorite' => function ($q) {
                            $q->where(
                                'user_id',
                                auth('api')->id()
                            );
                        },
                    ]);
                }
            )

        ->with([
            'translations',
            'category',
            'city.governorate',
            'images',
            'offer'
        ])
        ->paginate(
            $request->get('per_page', 10)
        );
}

public function getProviderOfferServices($request, $providerId)
{
    $provider = ServiceProvider::findOrFail($providerId);

    return Service::query()
        ->where('provider_id', $provider->user_id)
        ->where('is_active', true)
        ->whereHas('offer', function ($query) {
    $query->where('is_active', true)
        ->whereDate('end_date', '>=', today());
})
        ->when(
                auth('api')->check(),
                function ($query) {

                    $query->withExists([
                        'favorites as is_favorite' => function ($q) {
                            $q->where(
                                'user_id',
                                auth('api')->id()
                            );
                        },
                    ]);
                }
            )
        ->with([
            'translations',
            'category',
            'city.governorate',
            'images',
            'offer'
        ])
        ->paginate(
            $request->get('per_page', 10)
        );
}
}
