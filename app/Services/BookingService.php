<?php

namespace App\Services;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use function Termwind\renderUsing;

class BookingService
{
    public function getUserBookings($user, ?string $status)
    {
        return Booking::with(['service', 'customer', 'provider'])
            ->where(function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('provider_id', $user->id)
                            ->where('status', '!=', BookingStatus::DRAFT->value);
                    });
            })
            ->ofStatus($status)
            ->latest()
            ->paginate(15);
    }


    public function getBookingsByEvent(Event $event,?string $status)
    {
        $bookings =  $event->bookings()
            ->with(['service', 'customer', 'provider'])
            ->ofStatus($status)
            ->latest()
            ->get();

        return $bookings;
    }

    public function createBooking(array $data, $customerId): Booking
    {
        $service = Service::findOrFail($data['service_id']);

        $data['base_price'] = $service->base_price;
        $data['pricing_type'] = $service->pricing_type;
        $data['estimated_price'] = $this->calculateEstimatedPrice($service, $data);
        $data['status'] = BookingStatus::DRAFT->value;;
        $data['customer_id'] = $customerId;
        $data['provider_id'] = $service->provider_id;

        $booking = Booking::create($data);

        return $booking;
    }


    public function updateBooking(Booking $booking, array $data): Booking
    {

        $service = $booking->service;

        if (isset($data['duration']) || isset($data['quantity'])) {

            $priceContext = array_merge([
                'duration' => $booking->duration,
                'quantity' => $booking->quantity,
            ], $data);

            $data['estimated_price'] = $this->calculateEstimatedPrice($service, $priceContext);

        }

        $booking->update($data);


        return $booking->fresh(['service', 'customer', 'provider', 'event.city.governorate']);
    }


    public function respondToBooking(Booking $booking, string $action): Booking
    {
        //---- Notification ----
        if ($action === 'accept') {
            $booking->update([
                'status'      => BookingStatus::ACCEPTED->value,
                'accepted_at' => now(),
            ]);


        } elseif ($action === 'reject') {
            $booking->update([
                'status' => BookingStatus::REJECTED->value,
                'rejected_at' => now(),
            ]);

        }

        return $booking->fresh(['service', 'customer', 'provider', 'event.city.governorate']);
    }



    public function calculateEstimatedPrice(Service $service, array $data): float
    {

        $basePrice = (float) $service->base_price;

        $durationInHours = isset($data['duration']) ? ($data['duration'] / 60) : 1;
        $quantity = $data['quantity'] ?? 1;

        return match ($service->pricing_type->value) {
            'fixed'               => $basePrice,
            'per_hour'            => $basePrice * $durationInHours,
            'per_person'          => $basePrice * $quantity,
            'per_hour_per_person' => $basePrice * $durationInHours * $quantity,
            default               => $basePrice,
        };
    }
}
