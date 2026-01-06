<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PromoCode extends Model
{
    protected $fillable = [
      'title','code','type','type_reward','user_id','bonus_amount','premium_period','case_id','bonus_case_id','server_id','users','date_start','date_end','items', 'shop_item_id', 'variation_id'
    ];

}
