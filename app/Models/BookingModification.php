<?php

namespace App\Models;

use App\Enums\BookingModificationStatus;
use Illuminate\Database\Eloquent\Model;


class BookingModification extends Model
{
    protected $fillable = [
        'booking_id',
        'requested_by',
        'old_values',
        'new_values',
        'price_changed',
        'old_price',
        'new_price',
        'status',
        'requires_customer_approval',
        'requires_provider_approval',
        'customer_approved',
        'customer_responded_at',
        'provider_approved',
        'provider_responded_at',
        'respond_by',
    ];

    protected function casts(): array
    {
        return [
            'old_values'                 => 'array',
            'new_values'                 => 'array',
            'price_changed'              => 'boolean',
            'old_price'                  => 'decimal:2',
            'new_price'                  => 'decimal:2',
            'status'                     => BookingModificationStatus::class,
            'requires_customer_approval' => 'boolean',
            'requires_provider_approval' => 'boolean',
            'customer_approved'          => 'boolean',
            'customer_responded_at'      => 'datetime',
            'provider_approved'          => 'boolean',
            'provider_responded_at'      => 'datetime',
            'respond_by'                 => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }


    public function isFullyApproved(): bool
    {
        $customerOk = ! $this->requires_customer_approval || $this->customer_approved === true;
        $providerOk = ! $this->requires_provider_approval || $this->provider_approved === true;

        return $customerOk && $providerOk;
    }


    public function isRejectedByAnyRequiredParty(): bool
    {
        $customerRejected = $this->requires_customer_approval && $this->customer_approved === false;
        $providerRejected = $this->requires_provider_approval && $this->provider_approved === false;

        return $customerRejected || $providerRejected;
    }
}
