<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    use ResponseTrait;

    protected FavoriteService $favoriteService;

    protected string $resourceName =
        'messages.resources.favorite';

    public function __construct(
        FavoriteService $favoriteService
    ) {
        $this->favoriteService = $favoriteService;
    }

    
    public function store(FavoriteRequest $request)
    {
        $result = $this->favoriteService->addFavorite(
            auth('api')->id(),
            $request->validated('type'),
            $request->validated('id')
        );

        $favorite = $result['favorite'];
        $created = $result['created'];
        $favorite->load(['favoritable',]);

        if ($favorite->favoritable) {
            $favorite->favoritable->setAttribute('is_favorite',true);
        }

        return $this->apiResponse(new FavoriteResource($favorite),

            $created
                ? __('messages.created_success', [
                    'resource' => __($this->resourceName),
                ])
                : __('messages.resources.favorite already_exists'),

            $created
                ? Response::HTTP_CREATED
                : Response::HTTP_OK
        );
    }

  
    public function destroy(FavoriteRequest $request)
    {
        $deleted = $this->favoriteService->removeFavorite(
            auth('api')->id(),
            $request->validated('type'),
            $request->validated('id')
        );

        if (!$deleted) {
            return $this->apiResponse(
                null,

                __('messages.not_found', [
                    'resource' => __($this->resourceName),
                ]),

                Response::HTTP_NOT_FOUND
            );
        }

        return $this->apiResponse(
            null,

            __('messages.deleted_success', [
                'resource' => __($this->resourceName),
            ]),

            Response::HTTP_OK
        );
    }

    
    public function index()
    {
        $favorites = $this->favoriteService->getFavorites(auth('api')->id());

        return $this->apiResponse(
            $favorites->isNotEmpty()
                ? FavoriteResource::collection($favorites)
                : null,

            $favorites->isEmpty()
                ? __('messages.empty', [
                    'resource' => __($this->resourceName),
                ])
                : __('messages.fetched_success', [
                    'resource' => __($this->resourceName),
                ]),

            Response::HTTP_OK
        );
    }

   
    public function services()
    {
        $favorites = $this->favoriteService->getFavoriteServices(auth('api')->id());

        return $this->apiResponse(
            $favorites->isNotEmpty()
                ? FavoriteResource::collection($favorites)
                : null,

            $favorites->isEmpty()
                ? __('messages.empty', [
                    'resource' => __($this->resourceName),
                ])
                : __('messages.fetched_success', [
                    'resource' => __($this->resourceName),
                ]),

            Response::HTTP_OK
        );
    }

    
    public function providers()
    {
        $favorites = $this->favoriteService->getFavoriteProviders(auth('api')->id());

        return $this->apiResponse(
            $favorites->isNotEmpty()
                ? FavoriteResource::collection($favorites)
                : null,

            $favorites->isEmpty()
                ? __('messages.empty', [
                    'resource' => __($this->resourceName),
                ])
                : __('messages.fetched_success', [
                    'resource' => __($this->resourceName),
                ]),

            Response::HTTP_OK
        );
    }
}