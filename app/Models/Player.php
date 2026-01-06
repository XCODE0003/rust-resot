<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GameServer;

class Player extends Model
{
    protected $account;

    protected $fillable = [
        'login'
    ];

    protected $appends = [
        'characters_count',
        'last_login',
        'last_ip'
    ];

    protected $casts = [
        'last_login' => 'datetime'
    ];


    public function getCharactersCountAttribute()
    {
        $accounts = array($this->login);
        return GameServer::charactersCount($accounts);
    }

    public function getLastLoginAttribute()
    {
        if (!$this->account) {
            $this->account = GameServer::getUserAccounts($this->login);
        }
        if ($this->account && $this->account->last_login) {
            return date('d-m-Y', strtotime($this->account->last_login));
        }
        return null;
    }

    public function getLastIpAttribute()
    {
        if (!$this->account) {
            $this->account = GameServer::getUserAccounts($this->login);
        }
        if ($this->account && $this->account->last_ip) {
            return $this->account->last_ip;
        }
        return null;
    }

}
