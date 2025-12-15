<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $guard_name = 'sanctum';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'lat',
        'long',
        'address',
        'is_available'
    ];

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
            'lat' => 'float',
            'long' => 'float',
        ];
    }

    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
//
//    public function ordersAsDriver()
//    {
//        return $this->hasMany(Order::class, 'driver_id');
//    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable');
    }

    public function isCustomer()
    {
        return $this->hasRole('normal-user');
    }

    public function isDriver()
    {
        return $this->hasRole('driver');
    }

//    public function canAccessFilament(): bool
//    {
//        return $this->hasRole(['admin', 'super_admin']);
//    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    public function ratingStats(): array
    {
        $average = $this->receivedRatings()->avg('stars') ?? 0; // default to 0 if no ratings
        $count = $this->receivedRatings()->count();

        return [
            'avg' => round($average, 2),
            'count' => $count,
        ];
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }
}
