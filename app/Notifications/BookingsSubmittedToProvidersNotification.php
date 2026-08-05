<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Collection;

class BookingsSubmittedToProvidersNotification extends AppNotification
{

    public function __construct(
        private readonly Collection $bookings,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::NEW_BOOKING;
    }


    public function recipients(): array
    {
        return $this->bookings
            ->pluck('provider')
            ->unique('id')
            ->values()
            ->all();
    }

    public function messageFor(User $recipient): array
    {

        $providerBookings = $this->bookings->where('provider_id', $recipient->id);

        $firstBooking = $providerBookings->first();

        return [
            'title'   => __('notifications.booking_submitted.title'),
            'message' => $providerBookings->count() === 1
                ? __('notifications.booking_submitted.message_single', [
                    'service' => $firstBooking->service->title,
                ])
                : __('notifications.booking_submitted.message_multiple', [
                    'count' => $providerBookings->count(),
                ]),
            'object_id' => $firstBooking->id,
        ];
    }
}
