<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFcmToken extends Model
{
    protected $fillable = ['fcm_token'];
}
