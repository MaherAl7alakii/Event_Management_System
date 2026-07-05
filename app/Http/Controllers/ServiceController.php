<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Services\ServiceService;
use App\Traits\PaginationResponseTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;
    use PaginationResponseTrait;

    protected ServiceService $serviceService;


    protected string $resourceName = 'messages.resources.service';

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = $this->serviceService->getServices($request);

        return $this->apiResponse(
            !$services->isEmpty() ? [
                'pagination' => $this->formatPaginatedResponse($services),
                'services'   => ServiceResource::collection($services)
            ] : null,
            $services->isEmpty()
                ? __('messages.empty', ['resource' => __('messages.resources.services')])
                : __('messages.fetched_success', ['resource' => __('messages.resources.services')]),
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
//        dd($request->all());
        $service = $this->serviceService->createService(
            $request->validated()
        );

        return $this->apiResponse(
            new ServiceResource($service),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service = $this->serviceService->getService($service);

        return $this->apiResponse(
            new ServiceResource($service),
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $service = $this->serviceService->updateService(
            $service,
            $request->validated()
        );

        return $this->apiResponse(
            new ServiceResource($service),
            __('messages.updated_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $this->serviceService->deleteService($service);

        return $this->apiResponse(
            null,
            __('messages.deleted_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }



    public function searchSuggestions(Request $request)
    {
        $suggestions = $this->serviceService->getSearchSuggestions($request);

        return $this->apiResponse(
            !$suggestions->isEmpty() ? $suggestions : null,
            $suggestions->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourceName)])
                : __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }
}
