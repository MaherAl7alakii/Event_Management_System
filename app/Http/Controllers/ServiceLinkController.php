<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\ServiceLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceLinkController extends Controller
{
    public function __construct(
        private readonly ServiceLinkService $linkService
    ) {
    }

    /**
     * Display all linked services for a specific service.
     */
    public function index(Service $service): JsonResponse
    {
        $linkedIds = $this->linkService->linkedServiceIdsFor($service->id);

        $linkedServices = Service::whereIn('id', $linkedIds)->get();

        return response()->json([
            'status'  => 200,
            'message' => 'Linked services retrieved successfully.',
            'data'    => $linkedServices,
        ]);
    }

    /**
     * Link a service to another service.
     */
    public function store(Request $request, Service $service): JsonResponse
    {
        $request->validate([
            'linked_service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        if ($service->id == $request->linked_service_id) {
            return response()->json([
                'status'  => 422,
                'message' => 'You cannot link a service to itself.',
            ], 422);
        }

        $linkedService = Service::findOrFail($request->linked_service_id);

        $this->linkService->link($service, $linkedService);

        return response()->json([
            'status'  => 201,
            'message' => 'Service linked successfully.',
        ], 201);
    }

    /**
     * Unlink a service from another service.
     */
    public function destroy(Service $service, Service $linkedService): JsonResponse
    {
        $this->linkService->unlink($service, $linkedService);

        return response()->json([
            'status'  => 200,
            'message' => 'Service unlinked successfully.',
        ]);
    }
}
