<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'description',
        'address'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
