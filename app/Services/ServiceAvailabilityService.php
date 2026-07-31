<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use App\Models\TimeOff;
use App\Models\WorkingHour;
use Illuminate\Support\Carbon;


class ServiceAvailabilityService
{
    public function __construct(
        private readonly ServiceLinkService $serviceLinks,
    ) {
    }


    public function isAvailable(
        Service $service,
        string $bookingDate,
        string $startTime,
        ?int $duration = null,
        ?int $excludeBookingId = null,
    ): bool {
        return $this->unavailabilityReason($service, $bookingDate, $startTime, $duration, $excludeBookingId) === null;
    }


    public function unavailabilityReason(
        Service $service,
        string $bookingDate,
        string $startTime,
        ?int $duration = null,
        ?int $excludeBookingId = null,
    ): ?string {
        if (! $service->is_active) {
            return 'service_inactive';
        }

        $requestedStart = Carbon::parse("{$bookingDate} {$startTime}");
        $requestedEnd = $requestedStart->copy()->addMinutes($duration ?? 0);

        if (! $this->withinWorkingHours($service, $requestedStart, $requestedEnd)) {
            return 'outside_working_hours';
        }

        if ($this->hasTimeOffConflict($service, $requestedStart, $requestedEnd)) {
            return 'time_off_conflict';
        }


        if ($this->hasBookingConflict($service, $requestedStart, $requestedEnd, $excludeBookingId)) {
            return 'booking_conflict';
        }


        return null;
    }


    public function isDateAvailable(Service $service, string $bookingDate): bool
    {

        if (! $service->is_active) {
            return false;
        }

        $date = Carbon::parse($bookingDate);
        $serviceProviderId = $this->resolveServiceProviderId($service);


        $worksOnDay = WorkingHour::query()
            ->where('service_provider_id', $serviceProviderId)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->exists();

        if (! $worksOnDay) {
            return false;
        }



        return true;
    }



    private function withinWorkingHours(Service $service, Carbon $start, Carbon $end): bool
    {
        $serviceProviderId = $this->resolveServiceProviderId($service);

        $workingHour = WorkingHour::query()
            ->where('service_provider_id', $serviceProviderId)
            ->where('day_of_week', $start->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (! $workingHour) {
            return false;
        }

        $workStart = Carbon::parse($start->toDateString() . ' ' . $workingHour->start_time);
        $workEnd = Carbon::parse($start->toDateString() . ' ' . $workingHour->end_time);

        return $start->gte($workStart) && $end->lte($workEnd);
    }



    private function hasTimeOffConflict(Service $service, Carbon $start, Carbon $end): bool
    {
        $serviceProviderId = $this->resolveServiceProviderId($service);

        return TimeOff::query()
            ->where('service_provider_id', $serviceProviderId)
            ->overlapping($start, $end)
            ->get()
            ->contains(fn (TimeOff $timeOff) => $timeOff->overlapsWith($start, $end));
    }



    private function hasBookingConflict(
        Service $service,
        Carbon $requestedStart,
        Carbon $requestedEnd,
        ?int $excludeBookingId,
    ): bool {
        $relevantServiceIds = $this->serviceLinks
            ->linkedServiceIdsFor($service->id)
            ->push($service->id);

        $logicalRequestedEnd = $requestedStart->eq($requestedEnd)
            ? $requestedStart->copy()->addMinute()
            : $requestedEnd;

        $candidateBookings = Booking::query()
            ->whereIn('service_id', $relevantServiceIds)
            ->whereIn('status', $this->blockingBookingStatuses())
            ->whereDate('booking_date', $requestedStart->toDateString())
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->get();

        foreach ($candidateBookings as $existingBooking) {
            $existingStart = $existingBooking->startsAt();

            $existingEnd = $existingBooking->endsAtWithBuffer();
            $logicalExistingEnd = $existingStart->eq($existingEnd)
                ? $existingStart->copy()->addMinute()
                : $existingEnd;

            if ($requestedStart->lt($logicalExistingEnd) && $logicalRequestedEnd->gt($existingStart)) {
                return true;
            }
        }

        return false;
    }


    private function blockingBookingStatuses(): array
    {
        return [
            BookingStatus::PENDING->value,
            BookingStatus::ACCEPTED->value,
            BookingStatus::DEPOSIT_PAID->value,
            BookingStatus::CONFIRMED->value,
        ];
    }



    private function resolveServiceProviderId(Service $service): int
    {
        return $service->provider->serviceProvider->id;
    }
}
