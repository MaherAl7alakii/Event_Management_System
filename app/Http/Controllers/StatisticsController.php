<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
{
    use ResponseTrait;
    public function providerStatistics()
    {
        $providerId = auth()->id();


        $providerBookingsQuery = Booking::where('provider_id', $providerId);

        $todaysBookings = (clone $providerBookingsQuery)
            ->where('status', '!=', BookingStatus::DRAFT->value ?? 'draft')
            ->whereDate('created_at', Carbon::today())
            ->count();


        $pendingBookings = (clone $providerBookingsQuery)
            ->where('status', BookingStatus::PENDING->value ?? 'pending')
            ->count();

        // 4. الأرباح الشهرية (رقم ثابت مؤقتاً)
        $monthlyEarnings = 2500;

        $data = [
            'todays_bookings'  => $todaysBookings,
            'pending_requests' => $pendingBookings,
            'monthly_earnings' => $monthlyEarnings,
        ];

        return $this->apiResponse(
            $data,
            __('messages.fetched_success', ['resource' => __('Statistics')]),
            Response::HTTP_OK
        );
    }
}
