<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'phone',
        'avatar',
        'address',
        'gender',
        'birth_of_date',
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