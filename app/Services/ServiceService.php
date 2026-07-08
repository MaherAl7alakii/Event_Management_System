<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceTranslation;
use Illuminate\Support\Facades\DB;

class ServiceService
{

    public function getServices($request)
    {
        $services = Service::with(['category', 'city.governorate', 'images'])
            ->when(auth()->user()?->hasRole('provider'), function ($query) {
                $query->where('provider_id', auth()->id());
            }, function ($query) {
                $query->where('is_active', true);
            })
            ->filter($request->all())
            ->sort($request->query('sort_by'))
            ->paginate($request->query('per_page', 15))
            ->onEachSide(2);

        $searchKeyword = $request->query('search_key');
        $user = auth('api')->user();
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
        return $service->load('features');
    }



    public function createService(array $data)
    {
        return DB::transaction(function () use ($data) {

            $imagesData = $data['images'] ?? [];
            $featuresData = $data['features'] ?? [];

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

            return $service->refresh()->load('features');
        });
    }

    public function updateService(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {


            $imagesData = $data['images'] ?? [];
            $featuresData = $data['features'] ?? [];


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


}
