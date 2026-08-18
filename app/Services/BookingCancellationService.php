<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Enums\LedgerEntryType;
use App\Enums\ProviderPayoutReason;
use App\Enums\ProviderPayoutStatus;
use App\Models\Booking;
use App\Models\BookingLedgerEntry;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingCancellationService
{
    private const NO_REFUND_THRESHOLD_HOURS = 72;

    public function __construct(
        private readonly EventStatusResolver $statusResolver,
        private readonly ProviderPayoutService $payouts,
        private readonly LedgerService $ledger,
        private readonly BookingRefundAllocator $refundAllocator,
//         private readonly NotificationDispatcher $notifier, // (في حال قمت بتفعيل الإشعارات)
    ) {
    }

    public function cancel(Booking $booking, CancelledBy $cancelledBy, ?string $reason = null): Booking
    {
        $this->assertCancellable($booking);

        return DB::transaction(function () use ($booking, $cancelledBy, $reason) {


            if ($booking->package_id !== null) {

                $bookingsToCancel = Booking::where('event_id', $booking->event_id)
                    ->where('package_id', $booking->package_id)
                    ->whereNotIn('status', [
                        BookingStatus::COMPLETED->value,
                        BookingStatus::CANCELLED->value,
                        BookingStatus::REJECTED->value,
                        BookingStatus::EXPIRED->value,
                    ])
                    ->lockForUpdate()
                    ->get();
            } else {

                $bookingsToCancel = collect([Booking::lockForUpdate()->findOrFail($booking->id)]);
            }

            foreach ($bookingsToCancel as $lockedBooking) {

                match ($cancelledBy) {
                    CancelledBy::PROVIDER => $this->handleProviderCancellation($lockedBooking),
                    CancelledBy::CUSTOMER => $this->handleCustomerCancellation($lockedBooking),
                };

                $lockedBooking->update([
                    'status'       => BookingStatus::CANCELLED->value,
                    'cancelled_at' => now(),
                ]);

                Log::info('Booking cancelled', [
                    'booking_id'   => $lockedBooking->id,
                    'package_id'   => $lockedBooking->package_id,
                    'cancelled_by' => $cancelledBy->value,
                    'reason'       => $reason,
                ]);

                //---- Notification ----
                // $this->notifier->dispatch(new BookingCancelledNotification($lockedBooking, $cancelledBy, $reason));
            }


            $this->statusResolver->resolveAndPersist($booking->event);


            return $booking->fresh();
        });
    }

    private function assertCancellable(Booking $booking): void
    {
        $nonCancellable = [
            BookingStatus::COMPLETED,
            BookingStatus::CANCELLED,
            BookingStatus::REJECTED,
            BookingStatus::EXPIRED,
        ];

        if (in_array($booking->status, $nonCancellable, true)) {
            throw new Exception(
                "Booking #{$booking->id} cannot be cancelled from its current status ({$booking->status->value})."
            );
        }
    }

    private function handleProviderCancellation(Booking $booking): void
    {
        $this->payouts->cancelScheduledPayouts($booking);

        $netPaid = $this->ledger->netPaidByCustomer($booking);

        if ($netPaid > 0) {
            $this->refundAllocator->refundBookingShare($booking, $netPaid, 'provider_cancelled_booking');
        }
    }

    private function handleCustomerCancellation(Booking $booking): void
    {
        $netPaid = $this->ledger->netPaidByCustomer($booking);

        if ($netPaid <= 0) {
            $this->payouts->cancelScheduledPayouts($booking);
            return;
        }

        $depositShare = min($booking->depositAmount(), $netPaid);
        $extraPaid = round($netPaid - $depositShare, 2);

        if ($extraPaid <= 0) {
            $this->reconcilePayout($booking, $netPaid, ProviderPayoutReason::CANCELLATION_DEPOSIT_SHARE);
            return;
        }

        $hoursUntilBooking = now()->diffInHours($booking->startsAt(), false);

        if ($hoursUntilBooking <= self::NO_REFUND_THRESHOLD_HOURS) {
            $this->reconcilePayout($booking, $netPaid, ProviderPayoutReason::CANCELLATION_FULL_AMOUNT);
            return;
        }

        $this->reconcilePayout($booking, $depositShare, ProviderPayoutReason::CANCELLATION_DEPOSIT_SHARE);
        $this->refundAllocator->refundBookingShare($booking, $extraPaid, 'customer_cancelled_booking_partial_refund');
    }

    private function reconcilePayout(Booking $booking, float $amount, ProviderPayoutReason $reason): void
    {
        $payment = $this->latestContributingPayment($booking);

        $payout = $this->payouts->reconcileForBooking($booking, $amount, $reason, $payment);

        $payout?->update([
            'status'     => ProviderPayoutStatus::AWAITING_RELEASE->value,
            'release_at' => now()->addHours(24),
        ]);
    }

    private function latestContributingPayment(Booking $booking): ?Payment
    {
        $paymentId = BookingLedgerEntry::where('booking_id', $booking->id)
            ->whereIn('type', array_map(fn ($t) => $t->value, LedgerEntryType::chargeTypes()))
            ->whereNotNull('payment_id')
            ->orderByDesc('id')
            ->value('payment_id');

        return $paymentId ? Payment::find($paymentId) : null;
    }
}
