<?php

namespace App\Models;

use App\Enums\PriceProposalStatus;
use Illuminate\Database\Eloquent\Model;

class BookingPriceProposal extends Model
{
    protected $fillable = [
        'booking_id',
        'proposed_by',
        'old_price',
        'new_price',
        'status',
        'respond_by',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'old_price'    => 'decimal:2',
            'new_price'    => 'decimal:2',
            'status'       => PriceProposalStatus::class,
            'respond_by'   => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function proposedBy()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }
}
