<?php
namespace App\Lib\GameServer;

use Illuminate\Support\Facades\Facade;

class GameServerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GameServer';
    }
}