<?php

namespace App\Models;

use App\Enums\PricingType;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Favorite;
class Service extends Model
{
    use HasFactory, SoftDeletes ;
    use Translatable;


    public $translatedAttributes = [
        'title',
        'description',
        'address'
    ];

    protected $fillable = [
        'provider_id',
        'category_id',
        'city_id',
        'pricing_type',
        'base_price',
        'is_active',
        'min_hours',
        'max_hours',
        'max_guests',
        'rating',
        'rating_count',
        'reviews_count',
        'bookings_count',
    ];

    protected $casts = [

        'pricing_type' => PricingType::class,
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $durationInHours = isset($filters['duration']) ? ((float) $filters['duration'] / 60) : 1;
        $quantity = isset($filters['quantity']) ? (int) $filters['quantity'] : 1;
        $bothMultiplier = $durationInHours * $quantity;

        $table = $query->getModel()->getTable();

        return $query
            ->select("{$table}.*")
            ->selectRaw("
            (CASE pricing_type
                WHEN 'fixed' THEN base_price
                WHEN 'per_hour' THEN base_price * ?
                WHEN 'per_person' THEN base_price * ?
                WHEN 'per_hour_per_person' THEN base_price * ?
                ELSE base_price
            END) as estimated_price
        ", [$durationInHours, $quantity, $bothMultiplier])

            ->when($filters['search_key'] ?? null, function ($query, $search) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['city_id'] ?? null, function ($query, $cityId) {
                $query->where('city_id', $cityId);
            })
            ->when($filters['governorate_id'] ?? null, function ($query, $governorateId) {
                $query->whereHas('city', fn($q) => $q->where('governorate_id', $governorateId));
            })
            ->when($filters['pricing_type'] ?? null, function ($query, $pricingType) {
                $query->where('pricing_type', $pricingType);
            })
            ->when($filters['min_price'] ?? null, function ($query, $minPrice) {
                $query->where('base_price', '>=', $minPrice);
            })
            ->when($filters['max_price'] ?? null, function ($query, $maxPrice) {
                $query->where('base_price', '<=', $maxPrice);
            })
            ->when($filters['min_rating'] ?? null, function ($query, $minRating) {
                $query->where('rating', '>=', $minRating);
            })
            ->when($filters['service_provider_id'] ?? null, function ($query, $serviceProviderId) {
                $query->whereHas('provider.serviceProvider', function ($q) use ($serviceProviderId) {
                    $q->where('id', $serviceProviderId);
                });
            })
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $isActive = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN);
                $query->where('is_active', $isActive);
            })
            ->when($filters['budget'] ?? null, function ($query, $budget) {
                $query->having('estimated_price', '<=', $budget);
            })


            ->when($filters['sort_by_estimated_price'] ?? null, function ($query, $direction) {
                $query->orderBy('estimated_price', strtolower($direction) === 'desc' ? 'desc' : 'asc');
            });


    }


    public function scopeSort(Builder $query, ?string $sortType): Builder
    {
        return match ($sortType) {
            'price_asc'      => $query->orderBy('base_price', 'asc'),
            'price_desc'     => $query->orderBy('base_price', 'desc'),
            'highest_rating' => $query->orderBy('rating', 'desc'),
            'most_booked'    => $query->orderBy('bookings_count', 'desc'),
            'oldest'         => $query->orderBy('created_at', 'asc'),
            default          => $query->orderBy('created_at', 'desc'),
        };
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ServiceImage::class)
            ->where('is_primary', true);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }




  public function linkedServices()
  {
       $asOrigin = \App\Models\Service::query()
          ->whereIn('id', function ($q) {
                $q->select('linked_service_id')
                      ->from('service_links')
                      ->where('service_id', $this->id);
           });

      $asTarget = \App\Models\Service::query()
           ->whereIn('id', function ($q) {
                  $q->select('service_id')
                  ->from('service_links')
                      ->where('linked_service_id', $this->id);
           });

       return $asOrigin->union($asTarget)->get();

    }



public function offer()
{
    return $this->hasOne(ServiceOffer::class, 'service_id');
}


public function scopeWithActiveOffer($query)
{
    return $query->whereHas('offer', function ($q) {

        $q->where('is_active', true)->whereDate('start_date', '<=', today())->whereDate('end_date', '>=', today());

    });
}

public function packages()
{
    return $this->belongsToMany(Package::class,'package_service');
}
public function favorites(): MorphMany
{
    return $this->morphMany(Favorite::class, 'favoritable');
}
}
