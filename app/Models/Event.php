<?php

namespace App\Models;

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
        'confirmed_at'
    ];

    protected $casts = [
        'event_date'   => 'date',
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'status'       => EventStatus::class,
    ];


    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
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
}
