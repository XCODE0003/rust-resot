<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WonItem extends Model
{
    protected $fillable = [
      'user_id', 'item', 'item_id', 'item_icon', 'issued', 'server'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
