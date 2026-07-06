<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use Translatable;

    public array $translatedAttributes = ['name'];
}
