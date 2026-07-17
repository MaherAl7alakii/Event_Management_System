<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PricingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use  SoftDeletes;

    protected $fillable = [
        'service_id',
        'event_id',
        'customer_id',
        'provider_id',
        'pricing_type',
        'base_price',
        'estimated_price',
        'final_price',
        'service_date',
        'start_time',
        'duration',
        'quantity',
        'status',
        'customer_notes',
        'submitted_at',
        'accepted_at',
        'rejected_at',
        'confirmed_at',
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'start_time'   => 'datetime',
            'accepted_at'  => 'datetime',
            'rejected_at'  => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'pricing_type' => PricingType::class,
            'status'       => BookingStatus::class,
        ];
    }



    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        });
    }





    public function service()
    {
        return $this->belongsTo(Service::class);
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
