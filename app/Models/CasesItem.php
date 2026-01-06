<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CasesItem extends Model
{
    protected $fillable = [
      'item_id', 'category_id', 'quality_type', 'price', 'price_usd', 'amount', 'status', 'sort', 'image', 'source',
      'title',
      'subtitle',
      'description',
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        if (strlen($this->image) > 10) {
            return Storage::disk('public')->url($this->image);
        }
        return NULL;
    }

}
