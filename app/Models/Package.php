<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{

    use HasFactory;


    protected $fillable = [
        'service_provider_id',
        'image',
        'name',
        'description',
        'total_price',
        'discount',
        'final_price',
        'status',
    ];



    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class,'service_provider_id');
    }



    public function services()
    {
        return $this->belongsToMany(Service::class,'package_service');
    }

}