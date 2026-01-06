<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
      'path', 'banners'
    ];

    public function getImageUrl($image)
    {
        return Storage::disk('public')->url($image);
    }

}
