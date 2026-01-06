<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopSet extends Model
{
    protected $fillable = [
      'items', 'status', 'category_id', 'servers', 'server', 'price', 'price_usd', 'sort', 'amount', 'can_gift', 'image',
      'name_ru', 'name_en', 'name_de', 'name_fr', 'name_it', 'name_es', 'name_uk',
      'short_description_ru','short_description_en', 'short_description_de','short_description_fr', 'short_description_it','short_description_es', 'short_description_uk',
      'description_ru', 'description_en', 'description_de', 'description_fr', 'description_it', 'description_es', 'description_uk'
    ];

    protected $appends = [
        'image_url', 'items_arr'
    ];

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }
    public function getItemsArrAttribute()
    {
        $items_arr = [];
        $items = $this->items !== null ? json_decode($this->items) : [];
        foreach ($items as $item) {
            $shop_item = ShopItem::query()->where('status', 1)->where('id', $item->id)->first();
            if ($shop_item) {
                $shop_item->qty = $item->amount;

                if (!isset($items_arr[$shop_item->category_id])) {
                    $items_arr[$shop_item->category_id] = [];
                }

                $items_arr[$shop_item->category_id][] = $shop_item;
            }
        }

        if (auth()->id() === 497) {
            //dd($items_arr);
        }

        return $items_arr;
    }
}