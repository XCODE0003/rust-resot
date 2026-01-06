<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Shopping extends Model
{
    protected $fillable = [
      'user_id', 'command', 'status', 'server'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
