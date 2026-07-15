<?php

namespace App\Models;

use App\Enums\PricingType;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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
        return $query
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
            ->when(isset($filters['is_active']), function ($query) use ($filters) {
                $isActive = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN);
                $query->where('is_active', $isActive);
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




}
