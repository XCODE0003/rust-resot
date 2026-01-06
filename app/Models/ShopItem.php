<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopItem extends Model
{
    protected $fillable = [
        'rs_id', 'item_id', 'category_id', 'server', 'servers', 'price', 'price_usd', 'amount', 'command', 'is_blueprint', 'is_command', 'is_item', 'wipe_block', 'status', 'variations','image', 'sort', 'can_gift',
        'name_ru', 'name_en', 'name_de', 'name_fr', 'name_it', 'name_es', 'name_uk', 'short_name',
        'short_description_ru','short_description_en', 'short_description_de','short_description_fr', 'short_description_it','short_description_es', 'short_description_uk',
        'description_ru', 'description_en', 'description_de', 'description_fr', 'description_it', 'description_es', 'description_uk'
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }
}