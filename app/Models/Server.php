<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Server extends Model
{
    protected $fillable = [
      'id', 'name', 'status', 'sort', 'image', 'options', 'wipe', 'next_wipe', 'category_id'
    ];

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }
}
