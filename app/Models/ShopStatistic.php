<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopStatistic extends Model
{
    protected $fillable = [
      'item_id', 'set_id', 'case_id', 'amount', 'price', 'server', 'user_id', 'steam_id'
    ];
}
