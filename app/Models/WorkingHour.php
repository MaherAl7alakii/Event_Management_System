<?php

namespace App\Models;

use App\Support\TimeSpan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class WorkingHour extends Model
{
    protected $fillable = [
        'service_provider_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active'   => 'boolean',
        ];
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }


    public function windowFor(Carbon $calendarDate): array
    {
        return TimeSpan::resolve($calendarDate, $this->start_time, $this->end_time);
    }
}
