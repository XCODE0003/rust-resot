<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopCart extends Model
{
    protected $fillable = [
      'user_id', 'items', 'total', 'items_index'
    ];
}
