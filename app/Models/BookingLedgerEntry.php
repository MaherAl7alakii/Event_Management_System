<?php

namespace App\Models;

use App\Enums\LedgerEntryType;
use Illuminate\Database\Eloquent\Model;


class BookingLedgerEntry extends Model
{
    protected $fillable = [
        'booking_id',
        'type',
        'amount',
        'payment_id',
        'refund_id',
        'provider_payout_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'type'   => LedgerEntryType::class,
            'amount' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function providerPayout()
    {
        return $this->belongsTo(ProviderPayout::class);
    }
}
