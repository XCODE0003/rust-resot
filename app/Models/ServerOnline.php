<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerOnline extends Model
{
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    protected $table = 'server_online_data';

    protected $fillable = [
        'server_id', 'online_count', 'online_max', 'online_queued', 'players_data', 'updated_at'
    ];

    protected $casts = [
        'online_count' => 'integer',
        'online_max' => 'integer',
        'online_queued' => 'integer',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
