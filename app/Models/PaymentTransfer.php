<?php

namespace App\Models;

use App\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class PaymentTransfer extends Model
{
    protected $fillable = [
        'payment_id',
        'booking_id',
        'provider_id',
        'stripe_transfer_id',
        'amount',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => TransferStatus::class,
        ];
    }

    public function scopeSucceeded(Builder $query): Builder
    {
        return $query->where('status', TransferStatus::SUCCEEDED->value);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
