<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use Translatable;
    protected $fillable = ['service_id'];

    public $translatedAttributes = ['value'];


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getTranslatedValue(string $locale)
    {
        return $this->translations->where('locale', $locale)->first()?->value;
    }
}
