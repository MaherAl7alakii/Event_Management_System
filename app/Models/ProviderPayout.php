<?php

namespace App\Models;

use App\Enums\ProviderPayoutReason;
use App\Enums\ProviderPayoutStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class ProviderPayout extends Model
{
    protected $fillable = [
        'booking_id',
        'provider_id',
        'payment_id',
        'amount',
        'reason',
        'status',
        'release_at',
        'stripe_transfer_id',
        'failure_reason',
        'hold_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'reason'     => ProviderPayoutReason::class,
            'status'     => ProviderPayoutStatus::class,
            'release_at' => 'datetime',
        ];
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['provider_id'] ?? null, fn ($q, $providerId) => $q->where('provider_id', $providerId))
            ->when($filters['customer_id'] ?? null, fn ($q, $customerId) =>
            $q->whereHas('booking.event', fn ($eq) => $eq->where('customer_id', $customerId))
            )
            ->when($filters['from_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['to_date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeDueForRelease(Builder $query)
    {
        return $query
            ->where('status', ProviderPayoutStatus::AWAITING_RELEASE->value)
            ->where('release_at', '<=', now());
    }

    public function scopePendingOrHeld(Builder $query)
    {
        return $query->whereIn('status', [
            ProviderPayoutStatus::SCHEDULED_FOR_COMPLETION->value,
            ProviderPayoutStatus::AWAITING_RELEASE->value,
            ProviderPayoutStatus::ON_HOLD->value,
        ]);
    }
}
