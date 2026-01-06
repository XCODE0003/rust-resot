<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopCoupon extends Model
{
    protected $fillable = [
      'title','code','type','percent','user_id','users','date_start','date_end','items'
    ];
}
