<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceProviderGallery extends Model
{
protected $fillable = [
    'service_provider_id',
    'category_id',
    'type',
    'title',
    'path',
];

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
}