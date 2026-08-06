<?php

namespace App\Http\Controllers;

use App\Http\Requests\Offer\StoreOfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Service;
use App\Services\OfferService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{
    use ResponseTrait;

    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    
    public function store(StoreOfferRequest $request, Service $service): JsonResponse
    {
        $offer = $this->offerService->create($service,auth()->id(),$request->validated());

       return $this->apiResponse(
    new OfferResource($offer),
    'Offer created successfully.',
    Response::HTTP_CREATED
);
    }

    
    public function show(Service $service): JsonResponse
    {
        $offer = $this->offerService->show($service);

       return $this->apiResponse(
    new OfferResource($offer),
    'Offer retrieved successfully.',
    Response::HTTP_OK
);
    }

   
    public function update(StoreOfferRequest $request, Service $service): JsonResponse
    {
        $offer = $this->offerService->update($service,auth()->id(),$request->validated());

       return $this->apiResponse(
    new OfferResource($offer),
    'Offer updated successfully.',
    Response::HTTP_OK
);
    }

   
    public function destroy(Service $service): JsonResponse
    {
        $this->offerService->delete($service,auth()->id());

       return $this->apiResponse(
    null,
    'Offer deleted successfully.',
    Response::HTTP_OK
);
    }
}