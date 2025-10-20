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

//    public function acceptOffer(Offer $offer)
//    {
//        // transactional; assign driver, update status, mark offer accepted
//        DB::transaction(function() use ($offer) {
//            $this->driver_id = $offer->user_id;
//            $this->status = \App\Enums\OrderStatus::ASSIGNED;
//            $this->driver_assigned_at = now();
//            $this->save();
//
//            // mark offer accepted & others rejected
//            $this->offers()->update(['is_accepted' => false, 'rejected_at' => now()]);
//            $offer->is_accepted = true;
//            $offer->accepted_at = now();
//            $offer->rejected_at = null;
//            $offer->save();
//        });
//    }
}
