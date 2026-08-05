<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gallery\StoreGalleryRequest;
use App\Http\Requests\Gallery\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\CategoryResource;
use App\Models\ServiceProviderGallery;
use App\Services\GalleryService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GalleryController extends Controller
{
    use ResponseTrait;


    private GalleryService $galleryService;


    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }


    /**
     * Display provider gallery
     */
   public function index(
    int $serviceProviderId,
    \Illuminate\Http\Request $request
): JsonResponse
{

    $gallery = $this->galleryService->index(
        $serviceProviderId,
        $request->category_id
    );


    return $this->apiResponse(
        GalleryResource::collection($gallery),
        'Gallery retrieved successfully.',
        Response::HTTP_OK
    );
}


    /**
     * Store gallery item
     */
    public function store(StoreGalleryRequest $request): JsonResponse
    {
        $gallery = $this->galleryService->create(
            auth()->id(),
            $request->validated()
        );


        return $this->apiResponse(
            new GalleryResource($gallery),
            'Gallery item created successfully.',
            Response::HTTP_CREATED
        );
    }


    /**
     * Display single gallery item
     */
    public function show(ServiceProviderGallery $gallery): JsonResponse
    {
        return $this->apiResponse(
            new GalleryResource($gallery),
            'Gallery item retrieved successfully.',
            Response::HTTP_OK
        );
    }


    /**
     * Update gallery item
     */
    public function update(
        UpdateGalleryRequest $request,
        ServiceProviderGallery $gallery
    ): JsonResponse {
 
        $gallery = $this->galleryService->update(
            auth()->id(),
            $gallery,
            $request->validated()
        );


        return $this->apiResponse(
            new GalleryResource($gallery),
            'Gallery item updated successfully.',
            Response::HTTP_OK
        );
    }


    /**
     * Delete gallery item
     */
    public function destroy(ServiceProviderGallery $gallery): JsonResponse
    {
        $this->galleryService->delete(
            auth()->id(),
            $gallery
        );


        return $this->apiResponse(
            null,
            'Gallery item deleted successfully.',
            Response::HTTP_OK
        );
    }
    public function categories(int $serviceProviderId): JsonResponse
{
    $categories = $this->galleryService->categories($serviceProviderId);

    return $this->apiResponse(
        CategoryResource::collection($categories),
        'Gallery categories retrieved successfully.',
        Response::HTTP_OK
    );
}
}