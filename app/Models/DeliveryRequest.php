<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DeliveryRequest extends Model
{

    public $timestamps = false;

    protected $fillable = [
      'user_id', 'item_id', 'item', 'item_icon', 'item_type', 'amount', 'price', 'price_min', 'price_max', 'price_cap', 'status', 'delivery_id', 'note', 'server', 'date_request', 'date_execution'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
