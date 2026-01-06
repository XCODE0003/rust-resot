<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donate extends Model
{
    protected $fillable = ['user_id', 'server', 'payment_id', 'amount', 'bonus_amount', 'item_id', 'var_id', 'status', 'payment_system', 'steam_id'];
}
