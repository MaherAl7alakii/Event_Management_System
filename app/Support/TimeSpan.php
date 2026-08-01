<?php

namespace App\Support;

use Illuminate\Support\Carbon;


final class TimeSpan
{

    public static function resolve(Carbon $calendarDate, string $startTime, string $endTime): array
    {
        $start = Carbon::parse($calendarDate->toDateString() . ' ' . $startTime);
        $end = Carbon::parse($calendarDate->toDateString() . ' ' . $endTime);

        if ($end->lte($start)) {
            $end->addDay();
        }

        return [$start, $end];
    }
}
