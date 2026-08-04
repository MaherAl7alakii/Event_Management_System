<?php

namespace App\Services;

use App\Exceptions\TimeOffConflictException;
use App\Models\ServiceProvider;
use App\Models\TimeOff;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{

    public function createDefaultWorkingHours(ServiceProvider $provider)
    {
        return DB::transaction(function () use ($provider) {
            foreach (range(0, 6) as $day) {
                $isFriday = ($day === 5);

                $provider->workingHours()->updateOrCreate([
                    'day_of_week' => $day,
                    'is_active'   => ! $isFriday,
                    'start_time'  => $isFriday ? '00:00:00' : '09:00:00',
                    'end_time'    => $isFriday ? '00:00:00' : '23:00:00',
                ]);
            }

            return $provider->load('workingHours');
        });
    }

    public function updateWorkingHours(ServiceProvider $provider, array $days)
    {
        DB::transaction(function () use ($provider, $days) {
            foreach ($days as $day) {
                WorkingHour::updateOrCreate(
                    [
                        'service_provider_id' => $provider->id,
                        'day_of_week'          => $day['day_of_week'],
                    ],
                    [
                        'is_active'  => $day['is_active'],
                        'start_time' => $day['is_active'] ? $day['start_time'] : '00:00',
                        'end_time'   => $day['is_active'] ? $day['end_time'] : '00:00',
                    ],
                );
            }
        });

        return $provider->workingHours;
    }

    public function createTimeOff(ServiceProvider $provider, array $data): TimeOff
    {
        return DB::transaction(function () use ($provider, $data) {

            ServiceProvider::query()->find($provider->id);

            $candidate = new TimeOff([
                'service_provider_id' => $provider->id,
                'type'                => $data['type'],
                'start_date'          => $data['start_date'],
                'end_date'            => $data['end_date'] ?? $data['start_date'],
                'start_time'          => $data['start_time'] ?? null,
                'end_time'            => $data['end_time'] ?? null,
            ]);

            $this->assertNoConflict($provider, $candidate);

            $candidate->reason = $data['reason'] ?? null;
            $candidate->note = $data['note'] ?? null;
            $candidate->save();

            return $candidate;
        });
    }


    public function updateTimeOff(TimeOff $timeOff, array $data): TimeOff
    {
        return DB::transaction(function () use ($timeOff, $data) {
            ServiceProvider::query()->find($timeOff->service_provider_id);

            $candidate = (clone $timeOff)->fill([
                'type'       => $data['type'] ?? $timeOff->type,
                'start_date' => $data['start_date'] ?? $timeOff->start_date,
                'end_date'   => $data['end_date'] ?? $data['start_date'] ?? $timeOff->end_date,
                'start_time' => array_key_exists('start_time', $data) ? $data['start_time'] : $timeOff->start_time,
                'end_time'   => array_key_exists('end_time', $data) ? $data['end_time'] : $timeOff->end_time,
            ]);

            $this->assertNoConflict($timeOff->serviceProvider, $candidate, excludeId: $timeOff->id);

            $timeOff->update($data);

            return $timeOff->fresh();
        });
    }

    public function deleteTimeOff(TimeOff $timeOff): bool
    {
        return $timeOff->delete();
    }


    private function assertNoConflict(
        ServiceProvider $provider,
        TimeOff $candidate,
        ?int $excludeId = null,
    ): void {
        $candidateStart = Carbon::parse($candidate->start_date);
        $candidateEnd = Carbon::parse($candidate->end_date ?? $candidate->start_date);

        $possibleConflicts = TimeOff::query()
            ->where('service_provider_id', $provider->id)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->overlapping($candidateStart, $candidateEnd)
            ->get();

        $conflict = $possibleConflicts->first(
            fn (TimeOff $existing) => $candidate->conflictsWith($existing)
        );

        if ($conflict) {
            throw new TimeOffConflictException($conflict);
        }
    }
}
