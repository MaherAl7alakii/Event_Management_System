<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Booking;
use App\Models\User;


class BookingAcceptedNotification extends AppNotification
{
    public function __construct(
        private readonly Booking $booking,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::BOOKING_ACCEPTED;
    }

    public function recipients(): array
    {
        return [$this->booking->customer];
    }

    public function messageFor(User $recipient): array
    {
        return [
            'title'     => __('notifications.booking_accepted.title'),
            'message'   => __('notifications.booking_accepted.message', [
                'service' => $this->booking->service->title,
            ]),
            'object_id' => $this->booking->id,
        ];
    }
}
