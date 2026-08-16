<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\PackageService;
use App\Http\Resources\PackageResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use Illuminate\Http\Request;
class PackageController extends Controller
{

    use ResponseTrait;


    private PackageService $packageService;


    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }


   public function store(StorePackageRequest $request): JsonResponse
{

    $package = $this->packageService->create($request->validated());
    return $this->apiResponse(
        new PackageResource($package),
        'Package created successfully.',
        Response::HTTP_CREATED
    );

}


    public function update(UpdatePackageRequest $request,Package $package): JsonResponse
{
    $package = $this->packageService->update($package,$request->validated());
    return $this->apiResponse(
        new PackageResource($package),
        'Package updated successfully.',
        Response::HTTP_OK
    );

}


    public function destroy(Package $package): JsonResponse
    {
        $this->packageService->delete($package);
        return $this->apiResponse(
            null,
            'Package deleted successfully.',
            Response::HTTP_OK
        );

    }


    public function index(Request $request, $provider = null): JsonResponse
{
    $packages = $this->packageService->index($provider, $request);

    return $this->apiResponse(
        PackageResource::collection($packages),
        'Packages retrieved successfully.',
        Response::HTTP_OK
    );
}


    public function show(Package $package): JsonResponse
    {
        $package = $this->packageService->show($package);
        return $this->apiResponse(
            new PackageResource($package),
            'Package retrieved successfully.',
            Response::HTTP_OK
        );

    }

}