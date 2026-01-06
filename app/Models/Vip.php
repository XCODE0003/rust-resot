<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Vip extends Model
{
    protected $fillable = [
      'user_id', 'server_id', 'service_name', 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
