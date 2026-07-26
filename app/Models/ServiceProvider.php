<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceProvider extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'city_id',
        'account_type',
        'business_name',
        'avatar',
        'phone',
        'address',
        'approval_status',
        'years_of_experience',
        'description',
        'verified_at',
        'rejection_reason',
//        'main_service_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function categories()
    {
        return $this->hasMany(ServiceProviderCategory::class);
    }

    public function documents()
    {
        return $this->hasMany(ServiceProviderDocument::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

//    public function mainService()
//    {
//        return $this->belongsTo(Category::class, 'main_service_id');
//    }

//    public function governorate()
//{
//    return $this->belongsTo(Governorate::class);
//}


    public function workingHours()
   {
      return $this->hasMany(WorkingHour::class);
   }

  public function timeOffs()
  {
      return $this->hasMany(TimeOff::class);
  }


}
