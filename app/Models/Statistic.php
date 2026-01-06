<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Statistic extends Model
{
    public $timestamps = false;

    protected $fillable = [
      'date', 'general', 'server', 'player_id', 'name', 'user_id', 'deaths', 'kills', 'deaths_player', 'resourse_list', 'raid_list',
      'head_shots', 'is_npc', 'hits', 'shoots'
    ];

    protected $appends = [
        'avatar_url'
    ];

    public function getAvatarUrlAttribute()
    {
        $p_user = getuser_by_steamid($this->player_id);
        if (isset($p_user->avatar)) {
            $avatar = $p_user->avatar;
        } else {
            $avatar = '/images/bg/1-4.jpg';
        }
        return $avatar;
    }

    /*
    public function setPlayerIdAttribute($value) {
        $this->player_id = strval($value);
    }
    */
}