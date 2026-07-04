<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceProvider extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'account_type',
        'business_name',
        'avatar',
        'phone',
        'address',
        'approval_status',
        'years_of_experience',
        'description',
        'verified_at',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
   
}