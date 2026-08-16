<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PriceProposalStatus;
use App\Http\Resources\Booking\BookingShowResource;
use App\Http\Resources\BookingPriceProposalResource;
use App\Models\Booking;
use App\Models\BookingPriceProposal;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingPriceService
{
    public function __construct(
        private readonly BookingDeadlineCalculator $deadlines,
        private readonly BookingRefundAllocator $refundAllocator,
    ) {
    }


    public function proposeNewPrice(Booking $booking, float $newPrice, $proposedBy)
    {
        if ($newPrice <= 0) {
            throw new Exception('Price must be greater than zero.');
        }

        return DB::transaction(function () use ($booking, $newPrice, $proposedBy) {
            $locked = Booking::lockForUpdate()->findOrFail($booking->id);

            $this->assertPriceEditable($locked);


            if ($locked->status === BookingStatus::ACCEPTED) {
                $locked->update(['final_price' => round($newPrice, 2)]);

                Log::info('Booking price updated freely (no payment yet)', [
                    'booking_id' => $locked->id,
                    'new_price'  => $newPrice,
                ]);

                return new BookingShowResource($locked->fresh());
            }


            $this->expireStalePendingProposal($locked);

            $proposal = BookingPriceProposal::create([
                'booking_id'  => $locked->id,
                'proposed_by' => $proposedBy->id,
                'old_price'   => $locked->totalValue(),
                'new_price'   => round($newPrice, 2),
                'status'      => PriceProposalStatus::PENDING->value,
                'respond_by'  => $this->deadlines->confirmationDeadline($locked),
            ]);

            //---- Notification:

            Log::info('Price proposal created, awaiting customer approval', [
                'booking_id'  => $locked->id,
                'proposal_id' => $proposal->id,
            ]);

            return new BookingPriceProposalResource($proposal);
        });
    }


    public function respondToProposal(BookingPriceProposal $proposal, bool $accepted): Booking
    {
        return DB::transaction(function () use ($proposal, $accepted) {
            $locked = BookingPriceProposal::lockForUpdate()->findOrFail($proposal->id);

            if ($locked->status !== PriceProposalStatus::PENDING) {
                throw new Exception("Price proposal #{$locked->id} is no longer pending.");
            }

            if (now()->gt($locked->respond_by)) {
                $this->expireProposal($locked);

                throw new Exception("Price proposal #{$locked->id} has expired.");
            }

            $booking = Booking::lockForUpdate()->findOrFail($locked->booking_id);

            if (! $accepted) {
                $locked->update([
                    'status'       => PriceProposalStatus::REJECTED->value,
                    'responded_at' => now(),
                ]);

                Log::info('Customer rejected price proposal', ['proposal_id' => $locked->id]);

                //---- Notification

                return $booking;
            }

            $locked->update([
                'status'       => PriceProposalStatus::ACCEPTED->value,
                'responded_at' => now(),
            ]);

            return $this->applyAcceptedProposal($booking, $locked);
        });
    }




    private function applyAcceptedProposal(Booking $booking, BookingPriceProposal $proposal): Booking
    {
        $booking->update(['final_price' => $proposal->new_price]);
        $booking->refresh();

        $remaining = $booking->remainingBalance();

        if ($remaining <= 0) {

            $overpaid = $booking->overpaidAmount();

            if ($overpaid > 0) {
                $this->refundAllocator->refundBookingShare(
                    $booking,
                    $overpaid,
                    'price_reduced_after_payment_refund'
                );
            }

            if ($booking->status !== BookingStatus::CONFIRMED) {
                $booking->update([
                    'status'                    => BookingStatus::CONFIRMED->value,
                    'final_payment_deadline_at' => null,
                ]);

                Log::info('Booking confirmed after price change fully covered by prior payments', [
                    'booking_id' => $booking->id,
                ]);
            }

            //---- Notification

            return $booking->fresh();
        }

        if ($booking->status === BookingStatus::DEPOSIT_PAID) {

            Log::info('Price change absorbed into remaining final balance', ['booking_id' => $booking->id]);

            return $booking->fresh();
        }


        $booking->update([
            'status'                    => BookingStatus::DEPOSIT_PAID->value,
            'final_payment_deadline_at' => $this->deadlines->finalPaymentDeadline($booking->fresh()),
        ]);

        Log::warning('Booking reverted to deposit_paid due to accepted price increase after full payment', [
            'booking_id' => $booking->id,
        ]);

        //---- Notification

        return $booking->fresh();
    }


    private function expireStalePendingProposal(Booking $booking): void
    {
        $stale = BookingPriceProposal::where('booking_id', $booking->id)
            ->where('status', PriceProposalStatus::PENDING->value)
            ->first();

        if ($stale) {
            $this->expireProposal($stale);
        }
    }

    private function expireProposal(BookingPriceProposal $proposal): void
    {
        $proposal->update([
            'status'       => PriceProposalStatus::EXPIRED->value,
            'responded_at' => now(),
        ]);

        //---- Notification:

        Log::info('Price proposal expired without customer response', ['proposal_id' => $proposal->id]);
    }

    /**
     * @throws Exception
     */
    private function assertPriceEditable(Booking $booking): void
    {
        $editableStatuses = [
            BookingStatus::ACCEPTED,
            BookingStatus::DEPOSIT_PAID,
            BookingStatus::CONFIRMED,
        ];

        if (! in_array($booking->status, $editableStatuses, true)) {
            throw new Exception(
                "Booking #{$booking->id} price cannot be edited from its current status ({$booking->status->value})."
            );
        }
    }
}
