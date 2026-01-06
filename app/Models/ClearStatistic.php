<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ClearStatistic extends Model
{
    public $timestamps = false;

    protected $fillable = [
      'general', 'server', 'player_id', 'name', 'user_id', 'is_npc', 'deaths', 'kills', 'deaths_player', 'head_shots', 'hits', 'shoots', 'kdr', 'hits_kdr', 'date',
      'wood', 'stones', 'metal_ore', 'sulfur_ore', 'hq_metal_ore', 'leather', 'fat_animal', 'bone_fragments', 'cloth',
      'd_garage', 'd_wooden', 'd_metal', 'd_d_metal', 'd_d_wooden', 'd_d_armored', 'd_armored',
      'bb_wooden', 'bb_stone', 'bb_metal', 'bb_mvk', 'bb_reinf_w_glass', 'bb_auto_turret', 'bb_reinf_w_grilles',
    ];

    public function setPlayerIdAttribute($value) {
        $this->player_id = strval($this->player_id);
    }
}