<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\TimeOffType;
use App\Models\Booking;
use App\Models\ServiceProvider;
use App\Models\TimeOff;
use App\Models\WorkingHour;
use Illuminate\Support\Carbon;


class CalendarService
{
    /**
     * @return array{
     *     working_hours: array{start: string, end: string}|null,
     *     booked_hours: float,
     *     available_hours: float,
     *     unavailable_hours: float,
     *     bookings: \Illuminate\Support\Collection,
     *     time_offs: \Illuminate\Support\Collection,
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

        $bookings = $this->bookingsForDay($provider, $day);
        $timeOffs = $this->timeOffsForDay($provider, $day);


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

        $workStart = Carbon::parse($day->toDateString() . ' ' . $workingHour->start_time);
        $workEnd = Carbon::parse($day->toDateString() . ' ' . $workingHour->end_time);
        $totalWorkingMinutes = $workStart->diffInMinutes($workEnd);

        $bookedMinutes = $this->sumBookedMinutes($bookings, $workStart, $workEnd);
        $unavailableMinutes = $this->sumTimeOffMinutes($timeOffs, $workStart, $workEnd);


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
            ->whereBetween('service_date', [$from->toDateString(), $to->toDateString()])
            ->pluck('service_date')
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

    private function bookingsForDay(ServiceProvider $provider, Carbon $day)
    {
        return Booking::query()
            ->whereHas('service', fn ($q) => $q->where('provider_id', $provider->user_id))
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
                BookingStatus::CONFIRMED->value,
            ])
            ->whereDate('service_date', $day->toDateString())
            ->with(['service', 'customer'])
            ->orderBy('start_time')
            ->get();
    }

    private function timeOffsForDay(ServiceProvider $provider, Carbon $day)
    {
        return TimeOff::query()
            ->where('service_provider_id', $provider->id)
            ->overlapping($day, $day)
            ->get();
    }

    private function sumBookedMinutes($bookings, Carbon $workStart, Carbon $workEnd): int
    {
        $total = 0;

        foreach ($bookings as $booking) {
            $date = $workStart->toDateString();
            $start = Carbon::parse($date . ' ' . $booking->start_time->format('H:i:s'));
            $end = $booking->end_time !== null
                ? Carbon::parse($date . ' ' . $booking->end_time->format('H:i:s'))
                : $start->copy()->addMinutes((int) ($booking->duration ?? 0));

            $clampedStart = $start->max($workStart);
            $clampedEnd = $end->min($workEnd);

            if ($clampedEnd->gt($clampedStart)) {
                $total += $clampedStart->diffInMinutes($clampedEnd);
            }
        }

        return $total;
    }

    private function sumTimeOffMinutes($timeOffs, Carbon $workStart, Carbon $workEnd): int
    {
        $total = 0;

        foreach ($timeOffs as $timeOff) {
            if ($timeOff->type === TimeOffType::TIME_OFF) {
                $total += $workStart->diffInMinutes($workEnd);
                continue;
            }

            $date = $workStart->toDateString();
            $start = Carbon::parse($date . ' ' . $timeOff->start_time);
            $end = Carbon::parse($date . ' ' . $timeOff->end_time);

            $clampedStart = $start->max($workStart);
            $clampedEnd = $end->min($workEnd);

            if ($clampedEnd->gt($clampedStart)) {
                $total += $clampedStart->diffInMinutes($clampedEnd);
            }
        }

        return $total;
    }
}
