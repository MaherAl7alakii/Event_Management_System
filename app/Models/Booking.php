<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PricingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'service_id',
        'event_id',
        'customer_id',
        'provider_id',
        'pricing_type',
        'base_price',
        'estimated_price',
        'final_price',
        'deposit_amount_paid',
        'booking_date',
        'start_time',
        'duration',
        'quantity',
        'status',
        'customer_notes',
        'buffer_after_minutes',
        'submitted_at',
        'accepted_at',
        'rejected_at',
        'cancelled_at',
        'confirmed_at',
        'completed_at',
        'deposit_deadline_at',
        'final_payment_deadline_at',
        'payout_deadline_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date'               => 'date',
            'start_time'                 => 'datetime',
            'submitted_at'                => 'datetime',
            'accepted_at'                 => 'datetime',
            'rejected_at'                 => 'datetime',
            'cancelled_at'                => 'datetime',
            'confirmed_at'                => 'datetime',
            'completed_at'                => 'datetime',
            'deposit_deadline_at'         => 'datetime',
            'final_payment_deadline_at'   => 'datetime',
            'payout_deadline_at'          => 'datetime',
            'pricing_type'                => PricingType::class,
            'status'                      => BookingStatus::class,
            'deposit_amount_paid'         => 'decimal:2',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function complaints()
    {
        return $this->hasMany(BookingComplaint::class);
    }


    public function priceProposals()
    {
        return $this->hasMany(BookingPriceProposal::class)->latest();
    }


    public function activeComplaint()
    {
        return $this->hasOne(BookingComplaint::class)
            ->where('status', \App\Enums\ComplaintStatus::PENDING->value);
    }


    public function pendingPriceProposal()
    {
        return $this->hasOne(BookingPriceProposal::class)
            ->where('status', \App\Enums\PriceProposalStatus::PENDING->value)
            ->latestOfMany();
    }


    public function startsAt(): Carbon
    {
        return Carbon::parse(
            $this->booking_date->toDateString() . ' ' . $this->start_time->format('H:i:s')
        );
    }

    public function endsAt(): Carbon
    {
        return $this->startsAt()->copy()->addMinutes((int) ($this->duration ?? 0));
    }


    public function endsAtWithBuffer(): Carbon
    {
        return $this->endsAt()->copy()->addMinutes((int) ($this->buffer_after_minutes ?? 0));
    }


    public const DEPOSIT_PERCENTAGE = 0.30;


    public function totalValue(): float
    {
        return (float) ($this->final_price ?? $this->estimated_price);
    }


    public function depositAmount(): float
    {
        return round($this->totalValue() * self::DEPOSIT_PERCENTAGE, 2);
    }

    public function depositAmountActuallyPaid(): float
    {
        return (float) ($this->deposit_amount_paid ?? 0);
    }


    public function finalBalanceAmount(): float
    {
        if ($this->deposit_amount_paid !== null) {
            return max(round($this->totalValue() - $this->depositAmountActuallyPaid(), 2), 0);
        }

        return $this->totalValue();
    }
}
