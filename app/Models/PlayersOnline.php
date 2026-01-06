<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlayersOnline extends Model
{
    protected $fillable = [
        'steam_id', 'user_id', 'server', 'online_prev', 'online_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
