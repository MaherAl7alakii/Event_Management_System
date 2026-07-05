<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['value', 'locale'];
}
