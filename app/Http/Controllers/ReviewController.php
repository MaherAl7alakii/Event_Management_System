<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;


class ReviewController extends Controller
{

    use ResponseTrait;


    private ReviewService $reviewService;



    public function __construct(
        ReviewService $reviewService
    )
    {
        $this->reviewService = $reviewService;
    }



    public function store(
        StoreReviewRequest $request
    )
    {

        $review = $this->reviewService->create(
            auth()->id(),
            $request->validated()
        );


        return $this->apiResponse(
            new ReviewResource($review),
            'Review created successfully.',
            Response::HTTP_CREATED
        );

    }




    public function update(
        UpdateReviewRequest $request,
        Review $review
    )
    {

        $review = $this->reviewService->update(
            auth()->id(),
            $review,
            $request->validated()
        );


       return $this->apiResponse(
    new ReviewResource($review),
    'Review updated successfully.',
    Response::HTTP_OK
);
    }




    public function destroy(
        Review $review
    )
    {

        $this->reviewService->delete(
            auth()->id(),
            $review
        );


       return $this->apiResponse(
    null,
    'Review deleted successfully.',
    Response::HTTP_OK
);
    }





    public function index(
    int $serviceProvider
)
{

    $data = $this->reviewService
        ->providerReviews($serviceProvider);


    return $this->apiResponse(
        [
            'average_rating' => $data['average_rating'],
            'total_reviews' => $data['total_reviews'],
            'reviews' => ReviewResource::collection(
                $data['reviews']
            ),
        ],
        'Reviews retrieved successfully.',
        Response::HTTP_OK
    );

}

}