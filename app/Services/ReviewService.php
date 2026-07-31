<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;


class ReviewService
{


    public function create(
        int $userId,
        array $data
    ): Review {


        $exists = Review::where(
            'user_id',
            $userId
        )
        ->where(
            'service_provider_id',
            $data['service_provider_id']
        )
        ->exists();



        if($exists){

            abort(
                Response::HTTP_FORBIDDEN,
                'You already reviewed this provider.'
            );

        }



        $data['user_id'] = $userId;


        return Review::create($data);

    }




    public function update(
        int $userId,
        Review $review,
        array $data
    ): Review {


        $this->ensureOwnership(
            $review,
            $userId
        );


        $review->update($data);


        return $review->fresh();

    }





    public function delete(
        int $userId,
        Review $review
    ): bool {


        $this->ensureOwnership(
            $review,
            $userId
        );


        return $review->delete();

    }




    public function providerReviews(
    int $providerId
){

    $reviews = Review::with('user')
        ->where(
            'service_provider_id',
            $providerId
        )
        ->latest()
        ->get();


    return [
        'average_rating' => round(
            $reviews->avg('rating'),
            1
        ),

        'total_reviews' => $reviews->count(),

        'reviews' => $reviews
    ];

}



    private function ensureOwnership(
        Review $review,
        int $userId
    ): void {


        if($review->user_id !== $userId){

            abort(
                Response::HTTP_FORBIDDEN,
                'You cannot modify this review.'
            );

        }

    }


}