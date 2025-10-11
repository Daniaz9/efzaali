<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable =[
        'store_id',
        'name',
        'description',
        'price',
        'stock',
    ];


    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'total_price')->withTimestamps();
    }

    public function photos()
    {
        return $this->morphMany(\App\Models\Photo::class, 'imageable');
    }

}
