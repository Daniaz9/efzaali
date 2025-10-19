<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'price',
        'note',
        'is_accepted',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'price' => 'float',
        'is_accepted' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class);
    }
}
