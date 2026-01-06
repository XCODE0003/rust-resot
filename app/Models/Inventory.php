<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{

    protected $fillable = ['type', 'item', 'case_id', 'shop_item_id', 'amount', 'variation_id', 'balance', 'vip_period', 'deposit_bonus', 'user_id'];

    public static function getInventoryItems()
    {
        return DB::table('inventories')
            ->select('inventories.id as inventory_id', 'inventories.item_id as item_id', 'inventories.amount as amount', 'inventories.user_id as user_id', 'category_id', 'price', 'status',
                'title_en', 'title_ru', 'title_br', 'title_es', 'subtitle_en', 'subtitle_ru', 'subtitle_br', 'subtitle_es', 'description_en', 'description_ru', 'description_br', 'description_es', 'image', 'sort',
                'inventories.created_at as created_at', 'inventories.updated_at as updated_at')
            ->where('inventories.user_id', auth()->user()->id)
            ->where('inventories.type',  0)
            ->where('inventories.amount', '>', 0)
            ->leftJoin('cases_items', 'cases_items.item_id', '=', 'inventories.item_id');
    }
}
