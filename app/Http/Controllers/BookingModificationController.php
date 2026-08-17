<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceUnavailableException;
use App\Http\Requests\Booking\BookingModificationRequest;
use App\Http\Requests\Booking\BookingModificationResponseRequest;
use App\Http\Resources\Booking\BookingShowResource;
use App\Http\Resources\BookingComplaintResource;
use App\Http\Resources\BookingModificationResource;
use App\Models\Booking;
use App\Models\BookingModification;
use App\Services\BookingModificationService;
use App\Traits\ResponseTrait;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class BookingModificationController extends Controller
{
    use ResponseTrait;

    protected string $resourceName = 'messages.resources.modification';

    public function __construct(
        private readonly BookingModificationService $modifications,
    ) {
    }


    public function propose(BookingModificationRequest $request, Booking $booking)
    {
        abort_unless(
            in_array(auth()->id(), [$booking->customer_id, $booking->provider_id], true),
            403,
        );

        try {
            $result = $this->modifications->propose($booking, $request->validated(), auth()->user());
        } catch (ServiceUnavailableException $e) {
            return $this->apiResponse(['reason' => $e->reason], $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $result instanceof Booking
            ? new BookingShowResource($result)
            : new BookingModificationResource($result);

        return $this->apiResponse(
            $data,
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }


    public function history(Booking $booking)
    {
        abort_unless(
            in_array(auth()->id(), [$booking->customer_gid, $booking->provider_id], true),
            403,
        );

        $modifications = $booking->modifications()->with('requestedBy:id,name')->latest()->get();

        return $this->apiResponse(
            BookingModificationResource::collection($modifications),
            $modifications->isEmpty()
                ? __('messages.empty', ['resource' => __($this->resourceName)])
                : __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function respond(BookingModificationResponseRequest $request, BookingModification $modification)
    {
        $booking = $modification->booking;
        $userId = auth()->id();

        abort_unless(
            in_array($userId, [$booking->customer_id, $booking->provider_id], true),
            403,
        );

        try {
            $updated = $userId === $booking->provider_id
                ? $this->modifications->respondAsProvider(
                    $modification,
                    auth()->user(),
                    (bool) $request->validated('approved'),
                    $request->validated('new_price'),
                )
                : $this->modifications->respondAsCustomer(
                    $modification,
                    auth()->user(),
                    (bool) $request->validated('approved'),
                );
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->apiResponse(
            new BookingShowResource($updated),
            __('messages.updated_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }
}
