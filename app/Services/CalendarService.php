<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\TimeOffType;
use App\Models\Booking;
use App\Models\ServiceProvider;
use App\Models\TimeOff;
use App\Models\WorkingHour;
use App\Support\TimeSpan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * @return array{
     *     working_hours: array{start: string, end: string}|null,
     *     booked_hours: float,
     *     available_hours: float,
     *     unavailable_hours: float,
     *     bookings: Collection,
     *     time_offs: Collection,
     * }
     */
    public function getDayView(ServiceProvider $provider, string $date): array
    {
        $day = Carbon::parse($date)->startOfDay();

        $workingHour = WorkingHour::query()
            ->where('service_provider_id', $provider->id)
            ->where('day_of_week', $day->dayOfWeek)
            ->where('is_active', true)
            ->first();


        $bookings = $this->bookingsRelevantToDay($provider, $day);
        $timeOffs = $this->timeOffsRelevantToDay($provider, $day);

        if (! $workingHour) {
            return [
                'working_hours'     => null,
                'booked_hours'      => 0,
                'available_hours'   => 0,
                'unavailable_hours' => 0,
                'bookings'          => $bookings,
                'time_offs'         => $timeOffs,
            ];
        }

        [$workStart, $workEnd] = $workingHour->windowFor($day);
        $totalWorkingMinutes = $workStart->diffInMinutes($workEnd);

        $bookedMinutes = $this->sumBookedMinutes($bookings, $workStart, $workEnd);
        $unavailableMinutes = $this->sumTimeOffMinutes($timeOffs, $day, $workStart, $workEnd);

        $bookedMinutes = min($bookedMinutes, $totalWorkingMinutes);
        $unavailableMinutes = min($unavailableMinutes, $totalWorkingMinutes - $bookedMinutes);

        $availableMinutes = max($totalWorkingMinutes - $bookedMinutes - $unavailableMinutes, 0);

        return [
            'working_hours' => [
                'start' => $workingHour->start_time,
                'end'   => $workingHour->end_time,
            ],
            'booked_hours'      => round($bookedMinutes / 60, 1),
            'available_hours'   => round($availableMinutes / 60, 1),
            'unavailable_hours' => round($unavailableMinutes / 60, 1),
            'bookings'          => $bookings,
            'time_offs'         => $timeOffs,
        ];
    }

    public function getMonthOverview(ServiceProvider $provider, int $month, int $year): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $bookedDates = $this->bookedDatesInRange($provider, $monthStart, $monthEnd);
        $vacationDates = $this->vacationDatesInRange($provider, $monthStart, $monthEnd);

        $overview = [];

        foreach ($bookedDates as $date) {
            $overview[$date] = 'booking';
        }

        foreach ($vacationDates as $date) {
            $overview[$date] = isset($overview[$date]) ? 'booking_vacation' : 'vacation';
        }

        ksort($overview);

        return $overview;
    }

    private function bookedDatesInRange(ServiceProvider $provider, Carbon $from, Carbon $to): array
    {
        return Booking::query()
            ->whereHas('service', fn ($q) => $q->where('provider_id', $provider->user_id))
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
                BookingStatus::CONFIRMED->value,
            ])
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->pluck('booking_date')
            ->map(fn ($date) => $date->toDateString())
            ->unique()
            ->values()
            ->all();
    }

    private function vacationDatesInRange(ServiceProvider $provider, Carbon $from, Carbon $to): array
    {
        $timeOffs = TimeOff::query()
            ->where('service_provider_id', $provider->id)
            ->overlapping($from, $to)
            ->get();

        $dates = [];

        foreach ($timeOffs as $timeOff) {
            $rangeStart = $timeOff->start_date->max($from);
            $rangeEnd = $timeOff->end_date->min($to);

            for ($cursor = $rangeStart->copy(); $cursor->lte($rangeEnd); $cursor->addDay()) {
                $dates[] = $cursor->toDateString();
            }
        }

        return array_values(array_unique($dates));
    }


    private function bookingsRelevantToDay(ServiceProvider $provider, Carbon $day): Collection
    {
        $candidates = Booking::query()
            ->whereHas('service', fn ($q) => $q->where('provider_id', $provider->user_id))
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
                BookingStatus::CONFIRMED->value,
            ])
            ->whereBetween('booking_date', [
                $day->copy()->subDay()->toDateString(),
                $day->toDateString(),
            ])
            ->with(['service', 'customer'])
            ->orderBy('start_time')
            ->get();

        $dayStart = $day->copy();
        $dayEnd = $day->copy()->addDay();

        return $candidates->filter(function (Booking $booking) use ($day, $dayStart, $dayEnd) {
            if ($booking->booking_date->toDateString() === $day->toDateString()) {
                return true;
            }


            return $booking->startsAt()->lt($dayEnd) && $booking->endsAtWithBuffer()->gt($dayStart);
        })->values();
    }


    private function timeOffsRelevantToDay(ServiceProvider $provider, Carbon $day): Collection
    {
        $candidates = TimeOff::query()
            ->where('service_provider_id', $provider->id)
            ->overlapping($day->copy()->subDay(), $day)
            ->get();

        $dayStart = $day->copy();
        $dayEnd = $day->copy()->addDay();

        return $candidates->filter(
            fn (TimeOff $timeOff) => $timeOff->overlapsWith($dayStart, $dayEnd)
        )->values();
    }

    /**
     * @param  Collection<int, Booking>  $bookings
     */
    private function sumBookedMinutes(Collection $bookings, Carbon $workStart, Carbon $workEnd): int
    {
        $total = 0;

        foreach ($bookings as $booking) {
            $start = $booking->startsAt();
            $end = $booking->endsAt();


            $clampedStart = $start->max($workStart);
            $clampedEnd = $end->min($workEnd);

            if ($clampedEnd->gt($clampedStart)) {
                $total += $clampedStart->diffInMinutes($clampedEnd);
            }
        }

        return $total;
    }

    private function sumTimeOffMinutes(Collection $timeOffs, Carbon $day, Carbon $workStart, Carbon $workEnd): int
    {
        $total = 0;

        foreach ($timeOffs as $timeOff) {
            if ($timeOff->type === TimeOffType::TIME_OFF) {
                $total += $workStart->diffInMinutes($workEnd);
                continue;
            }


            [$blockStart, $blockEnd] = TimeSpan::resolve($timeOff->start_date, $timeOff->start_time, $timeOff->end_time);

            $clampedStart = $blockStart->max($workStart);
            $clampedEnd = $blockEnd->min($workEnd);

            if ($clampedEnd->gt($clampedStart)) {
                $total += $clampedStart->diffInMinutes($clampedEnd);
            }
        }

        return $total;
    }
}
