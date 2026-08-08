<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification as BaseNotification;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends BaseNotification
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
