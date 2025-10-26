<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'driver_id',
        'type',
        'delivery_type',
        'status',
        'pickup_address',
        'dropoff_address',
        'pickup_lat',
        'pickup_long',
        'dropoff_lat',
        'dropoff_long',
        'distance',
        'delivery_fee',
        'total_price',
        'description',
        'driver_assigned_at',
        'preparing_at',
        'picked_up_at',
        'on_the_way_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => OrderType::class,
            'status' => OrderStatus::class,
            'pickup_lat' => 'float',
            'pickup_long' => 'float',
            'dropoff_lat' => 'float',
            'dropoff_long' => 'float',
            'driver_assigned_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')->withPivot('quantity', 'total_price')->withTimestamps();
    }

    public function scopeOpenOrder($query)
    {
        return $query->where('status', \App\Enums\OrderStatus::PENDING)
            ->whereNull('driver_id');
    }

    public function scopeNearestToDriver( $query, float $driverLat, float $driverLong, ?float $maxDistance = null)
    {
        // Haversine formula in kilometers
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(pickup_lat)) * cos(radians(pickup_long) - radians(?)) + sin(radians(?)) * sin(radians(pickup_lat))))";

        // Add distance as a column
        $query->selectRaw("orders.*, $haversine AS distance", [$driverLat, $driverLong, $driverLat])
            ->orderBy('distance', 'asc');

        // Apply max distance filter if provided
        if ($maxDistance !== null) {
            $query->havingRaw('distance <= ?', [$maxDistance]);
        }

        return $query;
    }

}
