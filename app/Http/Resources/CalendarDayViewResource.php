<?php

namespace App\Http\Resources;

use App\Enums\TimeOffReason;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CalendarDayViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $bookings = collect($this['bookings'] ?? [])->map(function ($booking) {
            $startTime = Carbon::parse($booking['start_time']);
            $endTime   = $startTime->copy()->addMinutes($booking['duration'] ?? 0);

            return [
                'booking_id'            => $booking['id'],
                'title'         => $booking['service']['title'] ?? '',
                'customer_name' => $booking['customer']['name'] ?? '',
                'city_name'      => $booking['service']['city']['name']  ?? '',
                'governorate_name' => $booking['service']['city']['governorate']['name'] ?? '',
                'time_range'    => $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A'),
                'type'          => 'booking',
                'sort_time'     => $startTime->format('H:i:s'),
            ];
        });


        $timeOffs = collect($this['time_offs'] ?? [])->map(function ($timeOff) {
            $isBlockTime  = $timeOff['type']->value === 'block_time';
            $startTimeStr = $isBlockTime && ! empty($timeOff['start_time']) ? $timeOff['start_time'] : '00:00:00';

            $timeRange = 'Full Day';
            if ($isBlockTime && ! empty($timeOff['start_time']) && ! empty($timeOff['end_time'])) {
                $timeRange = Carbon::parse($timeOff['start_time'])->format('g:i A') . ' - ' . Carbon::parse($timeOff['end_time'])->format('g:i A');
            }


            return [
                'timeOff_id'         => $timeOff['id'],
                'title'      => $timeOff['reason'],
                'type_label' => $isBlockTime ? 'Blocked' : 'Vacation',
                'time_range' => $timeRange,
                'type'       => 'time_off',
                'sort_time'  => Carbon::parse($startTimeStr)->format('H:i:s'),
            ];
        });


        $elements = $bookings->concat($timeOffs)
            ->sortBy('sort_time')
            ->values()
            ->map(function ($item) {
                unset($item['sort_time']);
                return $item;
            });

        return [
            'working_hours' => $this->formatWorkingHours(),
            'booked'    => $this['booked_hours'] . 'h',
            'available' => $this['available_hours'] . 'h',
            'blocked'   => $this['unavailable_hours'] . 'h',
            'elements' => $elements,
        ];
    }

    private function formatWorkingHours(): ?string
    {
        if (! isset($this['working_hours']['start'], $this['working_hours']['end'])) {
            return null;
        }

        $start = Carbon::parse($this['working_hours']['start'])->format('g:i A');
        $end   = Carbon::parse($this['working_hours']['end'])->format('g:i A');

        return "{$start} - {$end}";
    }
}
