<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'service_provider_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}