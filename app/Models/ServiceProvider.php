<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Favorite;
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
        'stripe_account_id',
//        'main_service_id',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'verified_at' => 'datetime',
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
        return $this->belongsToMany(Category::class, 'service_provider_categories');
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

public function galleries()
{
    return $this->hasMany(ServiceProviderGallery::class);
}
public function reviews()
{
    return $this->hasMany(Review::class);
}
public function packages()
{
    return $this->hasMany(Package::class);
}
public function favorites(): MorphMany
{
    return $this->morphMany(Favorite::class, 'favoritable');
}

    public function hasCompletedStripeOnboarding(): bool
    {
        return ! empty($this->stripe_account_id);
    }
}
