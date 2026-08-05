<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectServiceProviderRequest;
use App\Models\ServiceProvider;
use App\Services\ServiceProviderApprovalService;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;


class ServiceProviderApprovalController extends Controller
{

    use ResponseTrait;


    public function __construct(
        private ServiceProviderApprovalService $approvalService
    ){}



    public function approve(ServiceProvider $serviceProvider)
    {
        $provider = $this->approvalService->approve($serviceProvider);
        return $this->apiResponse(
            $provider,
            "Service provider approved successfully",
            Response::HTTP_OK
        );
    }



    public function reject(RejectServiceProviderRequest $request,ServiceProvider $serviceProvider)
    {
        $provider = $this->approvalService->reject($serviceProvider,$request->reason);
        return $this->apiResponse(
            $provider,
            "Service provider rejected successfully",
            Response::HTTP_OK
        );

    }
public function resubmit()
{
    $provider = auth()->user()->serviceProvider;
    $provider = $this->approvalService->resubmit($provider);
    return $this->apiResponse(
        $provider,
        "Service provider resubmitted successfully",
        Response::HTTP_OK
    );
}
}