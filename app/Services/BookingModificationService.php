<?php

namespace App\Services;

use App\Enums\BookingModificationStatus;
use App\Enums\BookingStatus;
use App\Exceptions\ServiceUnavailableException;
use App\Models\Booking;
use App\Models\BookingModification;
use App\Models\User;
//use App\Notifications\BookingModificationProposedNotification;
//use App\Notifications\BookingModificationRespondedNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class BookingModificationService
{
    /** الحقول التي يمكن اقتراح تعديلها عبر هذا المسار. */
    private const MODIFIABLE_FIELDS = ['start_time', 'booking_date', 'duration', 'quantity', 'customer_notes'];

    /** الحقول التي يستدعي تغييرها إعادة فحص توفر الخدمة (تمس الجدول فعلياً). */
    private const SCHEDULE_AFFECTING_FIELDS = ['start_time', 'booking_date', 'duration'];

    public function __construct(
        private readonly ServiceAvailabilityService $availability,
        private readonly BookingDeadlineCalculator $deadlines,
//        private readonly NotificationDispatcher $notifier,
        private readonly BookingService $bookingService,
    ) {
    }

    public function propose(Booking $booking, array $changes, User $requestedBy): Booking|BookingModification
    {
        $changes = array_intersect_key($changes, array_flip(self::MODIFIABLE_FIELDS));

        if (empty($changes)) {
            throw new Exception('No modifiable fields were provided.');
        }

        $result = DB::transaction(function () use ($booking, $changes, $requestedBy) {
            $locked = Booking::lockForUpdate()->findOrFail($booking->id);

            $this->assertModifiable($locked);


            if (in_array($locked->status, [BookingStatus::DRAFT, BookingStatus::PENDING], true)) {
                return $this->applyImmediately($locked, $changes, $requestedBy);
            }

            $oldValues = $this->extractOldValues($locked, $changes);


            if ($this->affectsSchedule($changes)) {
                $this->assertNewScheduleAvailable($locked, $changes);
            }

            return $this->createPendingModification($locked, $changes, $oldValues, $requestedBy);
        });

//        if ($result instanceof BookingModification) {
//            $this->notifier->dispatch(new BookingModificationProposedNotification($result));
//        }

        return $result;
    }


    public function respondAsProvider(BookingModification $modification, User $provider, bool $approved, ?float $newPrice = null): Booking
    {
        [$booking, $resultingModification] = DB::transaction(function () use ($modification, $provider, $approved, $newPrice) {
            $locked = BookingModification::lockForUpdate()->findOrFail($modification->id);

            $this->assertPending($locked);

            $booking = Booking::lockForUpdate()->findOrFail($locked->booking_id);

            if ($provider->id !== $booking->provider_id) {
                throw new Exception('You are not the provider for this booking.');
            }

            if (! $locked->requires_provider_approval) {
                throw new Exception('Provider approval is not required for this modification.');
            }

            $this->handleProviderResponse($locked, $approved, $newPrice);

            $locked->refresh();

            return $this->resolveOutcome($booking, $locked);
        });

//        if ($resultingModification !== null) {
//            $this->notifier->dispatch(new BookingModificationRespondedNotification($resultingModification));
//        }

        return $booking;
    }


    public function respondAsCustomer(BookingModification $modification, User $customer, bool $approved): Booking
    {
        [$booking, $resultingModification] = DB::transaction(function () use ($modification, $customer, $approved) {
            $locked = BookingModification::lockForUpdate()->findOrFail($modification->id);

            $this->assertPending($locked);

            $booking = Booking::lockForUpdate()->findOrFail($locked->booking_id);

            if ($customer->id !== $booking->customer_id) {
                throw new Exception('You are not the customer for this booking.');
            }

            if (! $locked->requires_customer_approval) {
                throw new Exception('This modification does not require your approval — the provider has not attached a new price to it yet.');
            }

            $locked->update(['customer_approved' => $approved, 'customer_responded_at' => now()]);

            $locked->refresh();

            return $this->resolveOutcome($booking, $locked);
        });

//        if ($resultingModification !== null) {
//            $this->notifier->dispatch(new BookingModificationRespondedNotification($resultingModification));
//        }

        return $booking;
    }


    private function applyImmediately(Booking $booking, array $changes, User $requestedBy): Booking
    {
        if ($this->affectsSchedule($changes)) {
            $this->assertNewScheduleAvailable($booking, $changes);
        }

        $updates = $changes;

        $newEstimatedPrice = $this->recalculatePrice($booking, $changes);

        if ($newEstimatedPrice !== null) {
            $updates['estimated_price'] = $newEstimatedPrice;
        }

        $booking->update($updates);

        Log::info('Booking modified immediately (draft/pending) — no approval and no history record', [
            'booking_id'          => $booking->id,
            'requested_by'        => $requestedBy->id,
            'changes'             => $changes,
            'new_estimated_price' => $newEstimatedPrice,
        ]);

        return $booking->fresh();
    }


    private function recalculatePrice(Booking $booking, array $changes): ?float
    {
        if (! isset($changes['duration']) && ! isset($changes['quantity'])) {
            return null;
        }

        $context = [
            'duration' => $changes['duration'] ?? $booking->duration,
            'quantity' => $changes['quantity'] ?? $booking->quantity,
        ];

        return $this->bookingService->calculateEstimatedPrice($booking->service, $context);
    }


    private function createPendingModification(
        Booking $booking,
        array $changes,
        array $oldValues,
        User $requestedBy,
    ): BookingModification {
        $modification = BookingModification::create([
            'booking_id'                  => $booking->id,
            'requested_by'                => $requestedBy->id,
            'old_values'                  => $oldValues,
            'new_values'                  => $changes,
            'price_changed'               => false,
            'old_price'                   => $booking->totalValue(),
            'new_price'                   => null,
            'status'                      => BookingModificationStatus::PENDING->value,
            'requires_customer_approval'  => false,
            'requires_provider_approval'  => true,
            'respond_by'                  => $this->deadlines->confirmationDeadline($booking),
        ]);

        Log::info('Booking modification created, awaiting provider approval', [
            'booking_id'      => $booking->id,
            'modification_id' => $modification->id,
        ]);

        return $modification;
    }


    private function handleProviderResponse(BookingModification $modification, bool $approved, ?float $newPrice): void
    {
        $modification->update([
            'provider_approved'     => $approved,
            'provider_responded_at' => now(),
        ]);

        if (! $approved || $newPrice === null) {
            return;
        }

        if ($newPrice <= 0) {
            throw new Exception('New price must be greater than zero.');
        }

        $modification->update([
            'new_price'                  => round($newPrice, 2),
            'price_changed'              => true,
            'requires_customer_approval' => true,
        ]);

        Log::info('Provider approved modification with a new final price — customer approval now required', [
            'modification_id' => $modification->id,
            'new_price'        => round($newPrice, 2),
        ]);
    }


    private function resolveOutcome(Booking $booking, BookingModification $modification): array
    {
        if ($modification->isRejectedByAnyRequiredParty()) {
            $modification->update(['status' => BookingModificationStatus::REJECTED->value]);

            Log::info('Booking modification rejected', ['modification_id' => $modification->id]);

            return [$booking->fresh(), $modification->fresh()];
        }

        if ($modification->isFullyApproved()) {
            $applied = $this->applyApprovedModification($booking, $modification);

            return [$applied, $modification->fresh()];
        }


        return [$booking->fresh(), null];
    }

    private function applyApprovedModification(Booking $booking, BookingModification $modification): Booking
    {

        if ($this->affectsSchedule($modification->new_values)) {
            $this->assertNewScheduleAvailable($booking, $modification->new_values, excludeBookingId: $booking->id);
        }

        $updates = $modification->new_values;

        if ($modification->price_changed) {
            $updates['final_price'] = $modification->new_price;
        }

        $booking->update($updates);

        $modification->update(['status' => BookingModificationStatus::APPROVED->value]);

        Log::info('Booking modification approved and applied', [
            'booking_id'      => $booking->id,
            'modification_id' => $modification->id,
        ]);

        return $booking->fresh();
    }


    public function expire(BookingModification $modification, bool $notify = true): void
    {
        $modification->update(['status' => BookingModificationStatus::EXPIRED->value]);

        Log::info('Booking modification expired without full response', ['modification_id' => $modification->id]);

//        if ($notify) {
//            $this->notifier->dispatch(new BookingModificationRespondedNotification($modification->fresh()));
//        }
    }


    private function affectsSchedule(array $changes): bool
    {
        return ! empty(array_intersect(self::SCHEDULE_AFFECTING_FIELDS, array_keys($changes)));
    }


    private function assertPending(BookingModification $modification): void
    {
        if ($modification->status !== BookingModificationStatus::PENDING) {
            throw new Exception("Modification #{$modification->id} is no longer pending.");
        }

        if ($modification->respond_by !== null && now()->gt($modification->respond_by)) {
            $this->expire($modification, notify: false);

            throw new Exception("Modification #{$modification->id} has expired.");
        }
    }


    private function assertModifiable(Booking $booking): void
    {
        $nonModifiable = [
            BookingStatus::COMPLETED,
            BookingStatus::CANCELLED,
            BookingStatus::REJECTED,
            BookingStatus::EXPIRED,
        ];

        if (in_array($booking->status, $nonModifiable, true)) {
            throw new Exception(
                "Booking #{$booking->id} cannot be modified from its current status ({$booking->status->value})."
            );
        }
    }

    private function assertNewScheduleAvailable(Booking $booking, array $changes, ?int $excludeBookingId = null): void
    {
        $bookingDate = $changes['booking_date'] ?? $booking->booking_date->toDateString();
        $startTime = $changes['start_time'] ?? $booking->start_time->format('H:i');
        $duration = array_key_exists('duration', $changes) ? $changes['duration'] : $booking->duration;

        $reason = $this->availability->unavailabilityReason(
            service: $booking->service,
            bookingDate: $bookingDate,
            startTime: $startTime,
            duration: $duration,
            excludeBookingId: $excludeBookingId ?? $booking->id,
        );

        if ($reason !== null) {
            throw ServiceUnavailableException::forReason($booking->service, $reason);
        }
    }


    private function extractOldValues(Booking $booking, array $changes): array
    {
        $old = [];

        foreach (array_keys($changes) as $field) {
            $old[$field] = match ($field) {
                'start_time'   => $booking->start_time->format('H:i'),
                'booking_date' => $booking->booking_date->toDateString(),
                default        => $booking->{$field},
            };
        }

        $old['_price'] = $booking->totalValue();

        return $old;
    }
}
