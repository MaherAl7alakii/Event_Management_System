<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PricingType;
use App\Services\BookingService;
use App\Services\LedgerService;
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
        'package_id',
        'customer_id',
        'provider_id',
        'pricing_type',
        'base_price',
        'estimated_price',
        'final_price',
         'deposit_amount_paid',
        'start_time',
        'booking_date',
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

    public function ledgerEntries()
    {
        return $this->hasMany(BookingLedgerEntry::class);
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


    public function netPaidByCustomer(): float
    {
        return app(LedgerService::class)->netPaidByCustomer($this);
    }


    public function remainingBalance(): float
    {
        return max(round($this->totalValue() - $this->netPaidByCustomer(), 2), 0);
    }


    public function overpaidAmount(): float
    {
        return max(round($this->netPaidByCustomer() - $this->totalValue(), 2), 0);
    }

    public function isFullyPaid(): bool
    {
        return $this->netPaidByCustomer() > 0 && $this->remainingBalance() <= 0.0;
    }


    public function depositAmountActuallyPaid(): float
    {
        return $this->netPaidByCustomer();
    }


    public function finalBalanceAmount(): float
    {
        return $this->remainingBalance();
    }

    public function bookingLedgerEntrys()
    {
        return $this->hasMany(BookingLedgerEntry::class);
    }

    public function modifications()
    {
        return $this->hasMany(BookingModification::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getOriginalPriceAttribute(): float
    {
        if (! $this->package_id) {
            return (float) $this->estimated_price;
        }

        return app(BookingService::class)->calculateEstimatedPrice($this->service, [
            'duration' => $this->duration,
            'quantity' => $this->quantity,
        ]);
    }



//

}
