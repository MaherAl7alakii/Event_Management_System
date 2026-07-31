<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Carbon;


class BookingDeadlineCalculator
{
    private function confirmationMarginHours(): int
    {
        return (int) config('booking.confirmation_margin_hours', 72);
    }

    private function depositGraceHours(): int
    {
        return (int) config('booking.deposit_grace_hours', 48);
    }


    public function confirmationDeadline(Booking $booking): Carbon
    {
        return $this->serviceStartsAt($booking)->copy()
            ->subHours($this->confirmationMarginHours());
    }


    public function depositDeadline(Booking $booking): Carbon
    {
        if ($booking->accepted_at === null) {
            throw new \LogicException(
                "Cannot calculate deposit deadline for booking #{$booking->id}: not accepted yet."
            );
        }

        $graceDeadline = $booking->accepted_at->copy()->addHours($this->depositGraceHours());
        $confirmationDeadline = $this->confirmationDeadline($booking);


        return $graceDeadline->lt($confirmationDeadline) ? $graceDeadline : $confirmationDeadline;
    }


    public function finalPaymentDeadline(Booking $booking): Carbon
    {
        return $this->confirmationDeadline($booking);
    }


    private function serviceStartsAt(Booking $booking): Carbon
    {
        $date = $booking->booking_date->toDateString();
        $time = $booking->start_time->format('H:i:s');

        return Carbon::parse("{$date} {$time}");
    }
}
