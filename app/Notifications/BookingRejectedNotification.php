<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Booking;
use App\Models\User;


class BookingRejectedNotification extends AppNotification
{
    public function __construct(
        private readonly Booking $booking,
        private readonly ?string $rejectionReason = null,
    ) {
    }

    public function type(): NotificationType
    {
        return NotificationType::BOOKING_REJECTED;
    }

    public function recipients(): array
    {
        return [$this->booking->customer];
    }

    public function messageFor(User $recipient): array
    {
        return [
            'title'     => __('notifications.booking_rejected.title'),
            'message'   => __('notifications.booking_rejected.message', [
                'service' => $this->booking->service->title,
                'reason'  => $this->rejectionReason ?? '',
            ]),
            'object_id' => $this->booking->id,
        ];
    }
}
