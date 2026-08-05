<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOffer extends Model
{
    protected $fillable = [
        'service_id',
        'discount',
        'original_price',
        'offer_price',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date'=>'date',
        'end_date'=>'date',
        'is_active'=>'boolean'
    ];

public function service()
{
    return $this->belongsTo(Service::class);
}
}