<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\BookingPriceProposalRequest;
use App\Http\Requests\Booking\BookingPriceProposalResponseRequest;
use App\Http\Resources\Booking\BookingShowResource;
use App\Http\Resources\BookingPriceProposalResource;
use App\Models\Booking;
use App\Models\BookingPriceProposal;
use App\Services\BookingPriceService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookingPriceController extends Controller
{

    use ResponseTrait;
    use AuthorizesRequests;
    public function __construct(
        private readonly BookingPriceService $priceService,
    ) {
    }


    public function propose(BookingPriceProposalRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('propose', [BookingPriceProposal::class, $booking]);

        try {
            $result = $this->priceService->proposeNewPrice(
                $booking,
                (float) $request->validated('new_price'),
                auth()->user(),
            );
        } catch (Exception $e) {
            return $this->apiResponse(
                null,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $data = $result instanceof BookingPriceProposal
            ? new BookingPriceProposalResource($result)
            : $result;

        return $this->apiResponse(
            $data,
            __('messages.price_proposal_submitted_successfully'),
            Response::HTTP_CREATED // 201
        );
    }


    public function history(Booking $booking): JsonResponse
    {
        $this->authorize('viewHistory', [BookingPriceProposal::class, $booking]);


        $result = $booking->priceProposals()->with('proposedBy:id,name')->latest()->get();


        return $this->apiResponse(
            BookingPriceProposalResource::collection($result),
            __('messages.price_proposal_history_fetched_successfully'),
            Response::HTTP_OK // 200
        );
    }


    public function respond(BookingPriceProposalResponseRequest $request, BookingPriceProposal $proposal): JsonResponse
    {
        $this->authorize('respond', $proposal);

        try {
            $booking = $this->priceService->respondToProposal(
                $proposal,
                (bool) $request->validated('accepted'),
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return $this->apiResponse(
            new BookingShowResource($booking),
            __('messages.price_proposal_responded_successfully'),
            Response::HTTP_OK // 200
        );
    }
}
