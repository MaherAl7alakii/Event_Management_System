<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles,HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_banned',
        'banned_at',
    ];
    protected string $guard_name = 'api';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->withoutRole('admin')

            ->when(!empty($filters['role']), function ($q) use ($filters) {
                $q->whereHas('roles', fn($r) => $r->where('name', $filters['role']));
            })

            ->when(!empty($filters['approval_status']), function ($q) use ($filters) {
                $q->whereHas('serviceProvider', fn($sp) => $sp->where('approval_status', $filters['approval_status']));
            })


            ->when(!empty($filters['city_id']), function ($q) use ($filters) {
                $cityId = $filters['city_id'];
                $q->where(function ($sub) use ($cityId) {
                    $sub->whereHas('profile', fn($p) => $p->where('city_id', $cityId))
                        ->orWhereHas('serviceProvider', fn($sp) => $sp->where('city_id', $cityId));
                });
            })

            ->when(!empty($filters['governorate_id']), function ($q) use ($filters) {
                $governorateId = $filters['governorate_id'];

                $q->where(function ($sub) use ($governorateId) {

                    $sub->whereHas('profile.city', function ($c) use ($governorateId) {
                        $c->where('governorate_id', $governorateId);
                    })

                        ->orWhereHas('serviceProvider.city', function ($c) use ($governorateId) {
                            $c->where('governorate_id', $governorateId);
                        });
                });
            })

            ->when(isset($filters['is_banned']), function ($q) use ($filters) {
                $isBanned = filter_var($filters['is_banned'], FILTER_VALIDATE_BOOLEAN);
                $q->where('is_banned', $isBanned);
            })

            ->when(!empty($filters['gender']), function ($q) use ($filters) {
                $gender = $filters['gender'];


                $q->whereHas('profile', function ($p) use ($gender) {
                    $p->where('gender', $gender);
                });


            })


            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('profile', fn($p) => $p->where('phone', 'like', "%{$search}%"))
                        ->orWhereHas('serviceProvider', function ($sp) use ($search) {
                        $sp->where('phone', 'like', "%{$search}%")
                               ->orWhere('business_name', 'like', "%{$search}%");
                        });
                });
            });
    }


    public function validateForPassportPasswordGrant($password)
    {
        if (request()->input('is_social') === true) {
            return true;
        }

        return Hash::check($password, $this->password);
    }


    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }


    public function searchHistory()
    {
        return $this->hasMany(SearchHistory::class, 'user_id');
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class,'customer_id');
    }

    public function customerBookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }


    public function providerBookings()
    {
        return $this->hasMany(Booking::class, 'provider_id');
    }

public function reviews()
{
    return $this->hasMany(Review::class);
}
}
