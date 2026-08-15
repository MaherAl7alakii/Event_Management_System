<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Model;

class BookingComplaint extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'description',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => ComplaintStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
