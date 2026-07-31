<?php

namespace App\Services;

use App\Models\ServiceProvider;
use App\Models\ServiceProviderGallery;
use App\Models\ServiceProviderCategory;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class GalleryService
{

    /**
     * Create gallery item
     */
    public function create(int $userId, array $data): ServiceProviderGallery
    {
        $provider = $this->getProviderByUser($userId);


        // Check category belongs to provider
        if (isset($data['category_id'])) {

            $this->checkProviderCategory(
                $provider->id,
                $data['category_id']
            );

        }


        if (isset($data['file'])) {

            $data['path'] = $data['file']->store(
                'service_providers/gallery',
                'public'
            );

            unset($data['file']);
        }


        $data['service_provider_id'] = $provider->id;


        return ServiceProviderGallery::create($data);
    }



    /**
     * Update gallery item
     */
    public function update(
        int $userId,
        ServiceProviderGallery $gallery,
        array $data
    ): ServiceProviderGallery {


        $provider = $this->getProviderByUser($userId);


        $this->ensureOwnership(
            $gallery,
            $provider->id
        );


        if (isset($data['category_id'])) {

            $this->checkProviderCategory(
                $provider->id,
                $data['category_id']
            );

        }



        if (isset($data['file'])) {


            if ($gallery->path) {

                Storage::disk('public')
                    ->delete($gallery->path);

            }


            $data['path'] = $data['file']->store(
                'service_providers/gallery',
                'public'
            );


            unset($data['file']);

        }



        $gallery->update($data);


        return $gallery->fresh();

    }




    /**
     * Delete gallery item
     */
    public function delete(
        int $userId,
        ServiceProviderGallery $gallery
    ): bool {


        $provider = $this->getProviderByUser($userId);


        $this->ensureOwnership(
            $gallery,
            $provider->id
        );



        if ($gallery->path) {

            Storage::disk('public')
                ->delete($gallery->path);

        }



        return $gallery->delete();

    }





    /**
     * Get provider gallery
     */
    public function index(
        int $serviceProviderId,
        ?int $categoryId = null
    ) {


        $query = ServiceProviderGallery::where(
            'service_provider_id',
            $serviceProviderId
        );



        if ($categoryId) {

            $query->where(
                'category_id',
                $categoryId
            );

        }



        return $query->latest()->get();

    }




    /**
     * Get provider by user
     */
    private function getProviderByUser(int $userId): ServiceProvider
    {

        return ServiceProvider::where(
            'user_id',
            $userId
        )->firstOrFail();

    }





    /**
     * Check ownership
     */
    private function ensureOwnership(
        ServiceProviderGallery $gallery,
        int $providerId
    ): void {


        if ($gallery->service_provider_id !== $providerId) {

            abort(
                Response::HTTP_FORBIDDEN,
                'You are not allowed to modify this gallery item.'
            );

        }

    }





    /**
     * Check provider category
     */
    private function checkProviderCategory(
        int $providerId,
        int $categoryId
    ): void {


        $exists = ServiceProviderCategory::where(
            'service_provider_id',
            $providerId
        )
        ->where(
            'category_id',
            $categoryId
        )
        ->exists();



        if (!$exists) {

            abort(
                Response::HTTP_FORBIDDEN,
                'This category is not available for this provider.'
            );

        }

    }

}