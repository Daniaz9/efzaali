<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
    ];

    protected $appends = [];

    public function getLogoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable');
    }
}
