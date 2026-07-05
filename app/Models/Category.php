<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use Translatable;
    protected $fillable = ['icon'];

    public $translatedAttributes = ['name'];
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
