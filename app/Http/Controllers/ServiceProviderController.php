<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceProviderRequest;
use App\Http\Requests\UpdateServiceProviderRequest;
use App\Http\Resources\ServiceProviderResource;
use App\Services\ServiceProviderService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceProviderController extends Controller
{
    use ResponseTrait;

    private ServiceProviderService $providerService;

    public function __construct(ServiceProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function show(): JsonResponse
    {
        $provider = $this->providerService->getProviderByUserId(auth()->id());

        if (!$provider) {
            return $this->apiResponse(
                null,
                'Service provider profile not found.',
                Response::HTTP_OK
            );
        }

        return $this->apiResponse(
            new ServiceProviderResource($provider),
            'Service provider profile fetched successfully.',
            Response::HTTP_OK
        );
    }

    public function store(StoreServiceProviderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $provider = $this->providerService->updateOrCreateProvider(auth()->id(), $data);

        return $this->apiResponse(
            new ServiceProviderResource($provider),
            'Service provider profile created successfully.',
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateServiceProviderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $provider = $this->providerService->updateOrCreateProvider(auth()->id(), $data);

        return $this->apiResponse(
            new ServiceProviderResource($provider),
            'Service provider profile updated successfully.',
            Response::HTTP_OK
        );
    }
}