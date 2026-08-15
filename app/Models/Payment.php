<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'event_id',
        'booking_id',
        'stripe_charge_id',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'amount',
        'payment_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'payment_type' => PaymentType::class,
            'status'       => PaymentStatus::class,
        ];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->where('status', '!=', 'pending')
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['customer_id'] ?? null, fn ($q, $customerId) =>
            $q->whereHas('event', fn ($eq) => $eq->where('customer_id', $customerId))
            )
            ->when($filters['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
    }

    public function scopeOfType(Builder $query, PaymentType $type): Builder
    {
        return $query->where('payment_type', $type->value);
    }

    public function scopeSucceeded(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::SUCCEEDED->value);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }


    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }


//    public function transfers()
//    {
//        return $this->hasMany(PaymentTransfer::class);
//    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
