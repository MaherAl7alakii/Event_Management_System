<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'event_type_id',
        'other_type',
        'city_id',
        'title',
        'cover_image',
        'event_date',
        'start_time',
        'end_time',
        'guests_count',
        'notes',
        'status',
        'submitted_at',
        'confirmed_at',
    ];

    protected $casts = [
        'event_date'   => 'date',
        'start_time'   => 'datetime',
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'status'       => EventStatus::class,
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    public function getActionButtonMetaAttribute(): array
    {
        $status = $this->status instanceof EventStatus
            ? $this->status
            : EventStatus::from($this->status);

        return match ($status) {
            EventStatus::DRAFT => [
                'action' => $this->hasDraftBookings() ? 'submit' : 'none',
                'amount' => null,
            ],

            EventStatus::SUBMITTED, EventStatus::PARTIALLY_ACCEPTED => [
                'action' => 'waiting',
                'amount' => null,
            ],

            EventStatus::AWAITING_PAYMENT => $this->resolveDepositAction(),

            EventStatus::DEPOSIT_PAID => $this->resolvePostDepositAction(),

            default => [
                'action' => 'none',
                'amount' => null,
            ],
        };
    }

    private function hasDraftBookings(): bool
    {
        return $this->bookings()->where('status', BookingStatus::DRAFT->value)->exists();
    }

    private function hasBlockingBookings(): bool
    {
        return $this->bookings()
            ->whereIn('status', [BookingStatus::DRAFT->value, BookingStatus::PENDING->value])
            ->exists();
    }


    private function resolveDepositAction(): array
    {
        if ($this->hasBlockingBookings()) {
            return ['action' => 'waiting', 'amount' => null];
        }

        $amount = $this->calculateDepositAmount();

        return $amount > 0
            ? ['action' => 'pay_deposit', 'amount' => $amount]
            : ['action' => 'none', 'amount' => null];
    }


    private function resolvePostDepositAction(): array
    {
        if ($this->hasBlockingBookings()) {
            return ['action' => 'waiting', 'amount' => null];
        }

        $unpaidAcceptedAddOn = $this->bookings()
            ->where('status', BookingStatus::ACCEPTED->value)
            ->whereDoesntHave('payments', fn ($q) => $q->where('status', 'succeeded'))
            ->first();

        if ($unpaidAcceptedAddOn) {
            return [
                'action' => 'pay_addon',
                'amount' => $unpaidAcceptedAddOn->totalValue(),
            ];
        }

        $amount = $this->calculateRemainingBalance();

        return $amount > 0
            ? ['action' => 'pay_final_balance', 'amount' => $amount]
            : ['action' => 'none', 'amount' => null];
    }

    public function calculateDepositAmount(): float
    {
        $total = $this->bookings()
            ->where('status', BookingStatus::ACCEPTED->value)
            ->get()
            ->sum(fn (Booking $booking) => $booking->depositAmount());

        return round($total, 2);
    }


    public function calculateRemainingBalance(): float
    {
        $total = $this->bookings()
            ->whereIn('status', [
                BookingStatus::ACCEPTED->value,
                BookingStatus::DEPOSIT_PAID->value,
            ])
            ->get()
            ->sum(fn (Booking $booking) => $booking->finalBalanceAmount());

        return round($total, 2);
    }
}
