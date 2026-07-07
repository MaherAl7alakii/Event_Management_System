<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function serviceProviders()
{
    return $this->hasMany(ServiceProvider::class);
}


}
