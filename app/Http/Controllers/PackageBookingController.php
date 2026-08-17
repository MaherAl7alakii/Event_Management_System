<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\PackageBookingRequest;
use App\Http\Resources\Booking\BookingIndexResource;
use App\Models\Event;
use App\Models\Package;
use App\Services\PackageBookingService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Response;

class PackageBookingController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private readonly PackageBookingService $packageBookingService
    ) {}


    public function store(PackageBookingRequest $request, Event $event, Package $package)
    {

        try {
            $bookings = $this->packageBookingService->addPackageToEvent(
                $event,
                $package,
                $request->validated('services'),
                auth()->id()
            );

            return $this->apiResponse(
                BookingIndexResource::collection($bookings),
                __('messages.package_booked_successfully'),
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
