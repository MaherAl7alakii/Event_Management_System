<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\ServiceUnavailableException;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Service;
use App\Notifications\BookingAcceptedNotification;
use App\Notifications\BookingRejectedNotification;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        private readonly EventStatusResolver $statusResolver,
        private readonly ServiceAvailabilityService $availability,
        private readonly BookingDeadlineCalculator $deadlines,
        private readonly NotificationDispatcher $notifier,
    ) {
    }

    public function getUserBookings($user, ?string $status,?string $date )
    {
        return Booking::with(['service', 'customer', 'provider'])
            ->where('status', '!=', BookingStatus::DRAFT->value)
            ->unless($user->hasRole('admin'), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('customer_id', $user->id)
                        ->orWhere('provider_id', $user->id);
                });
            })
            ->ofStatus($status)
            ->when($date, fn ($query) => $query->whereDate('created_at', $date))
            ->latest()
            ->paginate(20);
    }

    public function getBookingsByEvent(Event $event, ?string $status)
    {
        return $event->bookings()
            ->with(['service', 'customer', 'provider'])
            ->ofStatus($status)
            ->latest()
            ->get();
    }


    public function createBooking(array $data, $customerId): Booking
    {
        $service = Service::findOrFail($data['service_id']);


        $this->assertAvailable(
            service: $service,
            bookingDate: $data['booking_date'],
            startTime: $data['start_time'],
            duration: $data['duration'] ?? null,
        );

        $data['base_price'] = $service->base_price;
        $data['pricing_type'] = $service->pricing_type;
        $data['estimated_price'] = $this->calculateEstimatedPrice($service, $data);
        $data['status'] = BookingStatus::DRAFT->value;
        $data['customer_id'] = $customerId;
        $data['provider_id'] = $service->provider_id;

        return Booking::create($data);
    }

    /**
     * @throws ServiceUnavailableException
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        $service = $booking->service;

        $touchesSchedule = isset($data['start_time']) || isset($data['duration']);

        if ($touchesSchedule) {
            $this->assertAvailable(
                service: $service,
                bookingDate: $booking->booking_date->toDateString(),
                startTime: $data['start_time'] ?? $booking->start_time->format('H:i'),
                duration: array_key_exists('duration', $data) ? $data['duration'] : $booking->duration,
                excludeBookingId: $booking->id,
            );
        }

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


    public function respondToBooking(Booking $booking, string $action, ?int $bufferAfterMinutes = null,?float $finalPrice = null): Booking
    {
        DB::transaction(function () use ($booking, $action, $bufferAfterMinutes) {
            if ($action === 'accept') {
                $this->assertAvailable(
                    service: $booking->service,
                    bookingDate: $booking->booking_date->toDateString(),
                    startTime: $booking->start_time->format('H:i'),
                    duration: $booking->duration,
                    excludeBookingId: $booking->id,
                );

                $booking->update([
                    'status'                => BookingStatus::ACCEPTED->value,
                    'accepted_at'            => now(),
                    'buffer_after_minutes'   => $bufferAfterMinutes,
                    'final_price' => round($finalPrice ?? (float) $booking->estimated_price, 2),
                ]);
                $booking->deposit_deadline_at = $this->deadlines->depositDeadline($booking);
                $booking->save();

            } elseif ($action === 'reject') {
                $booking->update([
                    'status'      => BookingStatus::REJECTED->value,
                    'rejected_at' => now(),
                ]);
            }

            $this->statusResolver->resolveAndPersist($booking->event);

        });

        $booking = $booking->fresh(['service', 'customer', 'provider', 'event.city.governorate']);

        match ($action) {
            'accept' => $this->notifier->dispatch(new BookingAcceptedNotification($booking)),
            'reject' => $this->notifier->dispatch(new BookingRejectedNotification($booking)),
            default  => null,
        };

        return $booking;
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

    public function checkAvailability(
        Service $service,
        string $bookingDate,
        ?string $startTime = null,
        ?int $duration = null
    ): array {

        if ($startTime === null) {
            $isDateAvailable = $this->availability->isDateAvailable($service, $bookingDate);

            if (! $isDateAvailable) {
                throw ServiceUnavailableException::forReason($service, 'outside_working_hours');
            }

            return [
                'stage'        => 'date_validated',
                'is_available' => true,
            ];
        }


        $this->assertAvailable(
            service: $service,
            bookingDate: $bookingDate,
            startTime: $startTime,
            duration: $duration,
        );

        return [
            'stage'        => $duration !== null ? 'full_slot_validated' : 'time_validated',
            'is_available' => true,
        ];
    }


    private function assertAvailable(
        Service $service,
        string $bookingDate,
        string $startTime,
        ?int $duration,
        ?int $excludeBookingId = null,
    ): void {
        $reason = $this->availability->unavailabilityReason(
            service: $service,
            bookingDate: $bookingDate,
            startTime: $startTime,
            duration: $duration,
            excludeBookingId: $excludeBookingId,
        );

        if ($reason !== null) {
            throw ServiceUnavailableException::forReason($service, $reason);
        }
    }
}
