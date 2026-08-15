<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Models\Booking;
use App\Models\BookingComplaint;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;


class BookingComplaintService
{
    public function __construct(
        private readonly ProviderPayoutService $payouts,
    ) {
    }


    public function complaintBooking(Booking $booking, User $customer, string $description): BookingComplaint
    {
        return DB::transaction(function () use ($booking, $customer, $description) {
            $existingOpen = BookingComplaint::where('booking_id', $booking->id)
                ->where('status', ComplaintStatus::PENDING->value)
                ->exists();

//            if ($existingOpen) {
//                throw new Exception("Booking #{$booking->id} already has an open complaint.");
//            }

            $complaint = BookingComplaint::create([
                'booking_id'  => $booking->id,
                'customer_id' => $customer->id,
                'description' => $description,
                'status'      => ComplaintStatus::PENDING->value,
            ]);

            $this->payouts->holdForComplaint($booking, "Complaint #{$complaint->id} opened by customer.");

            //---- Notification:

            return $complaint;
        });
    }


    public function resolveInFavorOfProvider(BookingComplaint $complaint, User $admin, ?string $note = null): BookingComplaint
    {
        return DB::transaction(function () use ($complaint, $admin, $note) {
            $complaint->update([
                'status'      => ComplaintStatus::RESOLVED_RELEASE->value,
                'resolved_by' => $admin->id,
                'admin_notes' => $note,
                'resolved_at' => now(),
            ]);

            $this->payouts->releaseHeldPayouts($complaint->booking);

            //---- Notification ----

            return $complaint->fresh();
        });
    }


    public function resolveInFavorOfCustomer(BookingComplaint $complaint, User $admin, ?string $note = null): BookingComplaint
    {
        return DB::transaction(function () use ($complaint, $admin, $note) {
            $complaint->update([
                'status'      => ComplaintStatus::RESOLVED_REFUND->value,
                'resolved_by' => $admin->id,
                'admin_notes' => $note,
                'resolved_at' => now(),
            ]);

            $this->payouts->cancelHeldPayouts($complaint->booking);

            //---- Notification ----

            return $complaint->fresh();
        });
    }
}
