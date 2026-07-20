<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
//use App\Events\BookingsSubmittedToProviders;
use App\Models\Booking;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\DB;


class EventSubmissionService
{
    public function __construct(
        private readonly PaymentGatewayService $gatewayService,
        private readonly EventStatusResolver $statusResolver,
    ) {
    }


    public function submit(Event $event): array
    {
        if ($this->isFirstSubmission($event)) {
            $intent = $this->gatewayService->createSubmissionFeeIntent($event);

            return [
                'requires_payment' => true,
                'client_secret'    => $intent['client_secret'],
                'payment_id'       => $intent['payment_id'],
                'amount'           => $intent['amount'],
            ];
        }

        $event = $this->submitDraftBookings($event);

        return [
            'requires_payment' => false,
            'event'            => $event,
        ];
    }


    public function completeFirstSubmissionAfterPayment(Event $event): Event
    {
        return $this->submitDraftBookings($event);
    }

    private function isFirstSubmission(Event $event): bool
    {
        $status = $event->status instanceof EventStatus
            ? $event->status
            : EventStatus::from($event->status);

        return $status === EventStatus::DRAFT;
    }


    private function submitDraftBookings(Event $event): Event
    {
        return DB::transaction(function () use ($event) {
            $draftBookings = Booking::where('event_id', $event->id)
                ->where('status', BookingStatus::DRAFT->value)
                ->get();

            if ($draftBookings->isEmpty()) {
                throw new Exception("Event #{$event->id} has no draft bookings to submit.");
            }

            Booking::where('event_id', $event->id)
                ->whereIn('id', $draftBookings->pluck('id'))
                ->update([
                    'status'       => BookingStatus::PENDING->value,
                    'submitted_at' => now(),
                ]);

            $event->refresh();
            $updatedEvent = $this->statusResolver->resolveAndPersist($event);

//            BookingsSubmittedToProviders::dispatch($draftBookings->fresh());

            return $updatedEvent;
        });
    }
}
