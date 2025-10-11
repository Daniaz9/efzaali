<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = ['path'];

    protected $appends=['small_path'];


    public function imageable()
    {
        return $this->morphTo();
    }

//    public function getPathAttribute($value)
//    {
//        return url(Storage::url($value));
//    }

    public function getPathAttribute($value)
    {
        return url('storage/' . $value);
    }

    public function getSmallPathAttribute()
    {
        $value = $this->attributes['path'] ?? '';
        if (!$value) return null;

        $extension = pathinfo($value, PATHINFO_EXTENSION);
        $filename = pathinfo($value, PATHINFO_FILENAME);
        $small = str_replace($filename . '.' . $extension, "{$filename}-small.{$extension}", $value);

        return url('storage/' . $small);
    }
}
