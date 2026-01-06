<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopPurchase extends Model
{
    protected $fillable = [
      'item_id', 'user_id', 'validity'
    ];
}
