<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\PaymentEligibilityException;
use App\Models\Booking;
use App\Models\Event;
use Exception;
use Illuminate\Support\Collection;


class BookingEligibilityService
{

    public function assertEligibleForDeposit(Event $event): void
    {
        $this->assertNoBlockingBookings($event);

        if ($this->acceptedBookings($event)->isEmpty()) {
            throw new PaymentEligibilityException(
                "Event #{$event->id} has no accepted bookings awaiting deposit payment."
            );
        }
    }


    public function assertEligibleForFinalBalance(Event $event): void
    {
        $this->assertNoBlockingBookings($event);

        if ($this->finalBalanceBookings($event)->isEmpty()) {
            throw new PaymentEligibilityException(
                "Event #{$event->id} has no bookings awaiting final balance payment."
            );
        }
    }


    private function assertNoBlockingBookings(Event $event): void
    {
        if ($this->blockingBookings($event)->isNotEmpty()) {
            throw new PaymentEligibilityException(
                "Event #{$event->id} has bookings still pending admin/provider decision "
                . '(draft or pending) and cannot be paid for yet.'
            );
        }
    }


    public function blockingBookings(Event $event): Collection
    {
        return $event->bookings()
            ->whereIn('status', [
                BookingStatus::DRAFT->value,
                BookingStatus::PENDING->value,
            ])
            ->get();
    }


    public function acceptedBookings(Event $event): Collection
    {
        return $event->bookings()
            ->where('status', BookingStatus::ACCEPTED->value)
            ->get();
    }


    public function finalBalanceBookings(Event $event): Collection
    {
        return $event->bookings()
            ->whereIn('status', [
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
            ])
            ->get();
    }
}
