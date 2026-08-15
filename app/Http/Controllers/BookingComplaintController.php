<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingComplaintRequest;
use App\Http\Requests\Booking\ResolveComplaintRequest;
use App\Http\Resources\BookingComplaintResource;
use App\Models\Booking;
use App\Models\BookingComplaint;
use App\Services\BookingComplaintService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class BookingComplaintController extends Controller
{
    use ResponseTrait;
    use AuthorizesRequests;

    protected string $resourceName = 'messages.resources.booking_complaint';
    public function __construct(
        private readonly BookingComplaintService $complaints,
    ) {
    }

    public function index(Request $request)
    {
        $complaints = BookingComplaint::query()
            ->with(['booking.provider', 'booking.customer', 'customer'])

            ->when($request->filled('customer_id'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('customer_id', $request->customer_id)
                        ->orWhereHas('booking', fn ($b) => $b->where('customer_id', $request->customer_id));
                });
            })

            ->when($request->filled('provider_id'), function ($query) use ($request) {
                $query->whereHas('booking', function ($q) use ($request) {
                    $q->where('provider_id', $request->provider_id);
                });
            })
            ->latest()
            ->get();

        return $this->apiResponse(

            !$complaints->isEmpty() ? BookingComplaintResource::collection($complaints) : null,

            $complaints->isEmpty()

                ? __('messages.empty', ['resource' => __('messages.resources.booking_complaints')])

                : __('messages.fetched_success', ['resource' => __('messages.resources.booking_complaints')]),

            Response::HTTP_OK

        );

    }



    public function show(BookingComplaint $complaint)
    {


        $complaint->load(['booking.service', 'booking.customer', 'booking.provider']);

        return $this->apiResponse(
            new BookingComplaintResource($complaint),
            __('messages.fetched_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_OK
        );
    }


    public function complaintBooking(BookingComplaintRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('create', [BookingComplaint::class, $booking]);

        try {
            $complaint = $this->complaints->complaintBooking(
                $booking,
                auth()->user(),
                $request->validated('description'),
            );
        } catch (Exception $e) {
            return $this->apiResponse(
                null,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->apiResponse(
            new BookingComplaintResource($complaint),
            __('messages.created_success', ['resource' => __($this->resourceName)]),
            Response::HTTP_CREATED
        );
    }

    public function resolve(ResolveComplaintRequest $request, BookingComplaint $complaint): JsonResponse
    {
        $admin = auth()->user();

        $data = $request->validated();

        if ($complaint->booking?->payout_status === 'released') {
            return $this->apiResponse(
                null,
                __('messages.complaint_cannot_be_resolved_payout_already_released'),
                Response::HTTP_UNPROCESSABLE_ENTITY // 422
            );
        }


        $resolved = match ($data['decision']) {
            'release_to_provider' => $this->complaints->resolveInFavorOfProvider($complaint, $admin, $data['note'] ?? null),
            'refund_to_customer'  => $this->complaints->resolveInFavorOfCustomer($complaint, $admin, $data['note'] ?? null),
        };

        return response()->json(new BookingComplaintResource($resolved));
    }
}
