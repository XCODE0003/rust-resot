<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePurchase extends Model
{
    protected $fillable = [
      'user_id', 'server', 'command'
    ];
}
