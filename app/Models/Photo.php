<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['path', 'small_path', 'alt_text', 'is_main'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getPathAttribute($value)
    {
        return url(Storage::url($value));
    }

    public function getSmallPathAttribute($value)
    {
        return url(Storage::url($value));
    }
}
