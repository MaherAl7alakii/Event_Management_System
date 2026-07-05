<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use Translatable;
    protected $fillable = ['governorate_id'];

    public $translatedAttributes = ['name'];


    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
