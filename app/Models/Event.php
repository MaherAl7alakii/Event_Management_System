<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
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
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }


    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
