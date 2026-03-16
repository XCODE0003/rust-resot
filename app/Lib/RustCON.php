<?php
namespace App\Lib;

use WebSocket\BadOpcodeException;
use WebSocket\Client;
use WebSocket\TimeoutException;
use Illuminate\Support\Facades\Log;
use App\Services\RconConnectionManager;

class RustCON
{

    public static function sendCommand($command, $server_id = null)
    {
        if (config('server_api.rcon_ip', '') == '' || strpos(config('server_api.rcon_ip', ''), '127.0.0.1') !== FALSE) return FALSE;

        if ($server_id === null) {
            $server_id = session('server_id', 1);
        }

        try {
            $manager = RconConnectionManager::getInstance();
            $result = $manager->sendCommand($server_id, $command);
            
            return $result;

        } catch (\Exception $ex) {
            Log::channel('rcon')->error("Error sending command: {$ex->getMessage()}");
        }

        return FALSE;
    }
}
