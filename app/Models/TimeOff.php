<?php

namespace App\Models;

use App\Enums\TimeOffReason;
use App\Enums\TimeOffType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class TimeOff extends Model
{
    protected $fillable = [
        'service_provider_id',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'reason',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'type'       => TimeOffType::class,
            'start_date' => 'date',
            'end_date'   => 'date',
            'reason' => TimeOffReason::class,

        ];
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }


    public function scopeOverlapping(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query
            ->whereDate('start_date', '<=', $to->toDateString())
            ->whereDate('end_date', '>=', $from->toDateString());
    }


    public function overlapsWith(Carbon $from, Carbon $to): bool
    {
        if ($to->toDateString() < $this->start_date->toDateString()
            || $from->toDateString() > $this->end_date->toDateString()) {
            return false;
        }

        if ($this->type === TimeOffType::TIME_OFF) {
            return true;
        }


        $blockStart = Carbon::parse($this->start_date->toDateString() . ' ' . $this->start_time);
        $blockEnd = Carbon::parse($this->start_date->toDateString() . ' ' . $this->end_time);

        return $from->lt($blockEnd) && $to->gt($blockStart);
    }


    public function conflictsWith(self $other): bool
    {
        if ($this->type === TimeOffType::TIME_OFF || $other->type === TimeOffType::TIME_OFF) {
            return $this->dateRangesOverlap($other);
        }


        if ($this->start_date->toDateString() !== $other->start_date->toDateString()) {
            return false;
        }

        $thisStart = Carbon::parse($this->start_date->toDateString() . ' ' . $this->start_time);
        $thisEnd = Carbon::parse($this->start_date->toDateString() . ' ' . $this->end_time);
        $otherStart = Carbon::parse($other->start_date->toDateString() . ' ' . $other->start_time);
        $otherEnd = Carbon::parse($other->start_date->toDateString() . ' ' . $other->end_time);

        return $thisStart->lt($otherEnd) && $thisEnd->gt($otherStart);
    }

    private function dateRangesOverlap(self $other): bool
    {
        return $this->start_date->toDateString() <= $other->end_date->toDateString()
            && $this->end_date->toDateString() >= $other->start_date->toDateString();
    }
}
