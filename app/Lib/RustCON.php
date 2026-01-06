<?php
namespace App\Lib;

use WebSocket\BadOpcodeException;
use WebSocket\Client;
use WebSocket\TimeoutException;
use Illuminate\Support\Facades\Log;

class RustCON
{

    public static function sendCommand($command)
    {
        if (config('server_api.rcon_ip', '') == '' || strpos(config('server_api.rcon_ip', ''), '127.0.0.1') !== FALSE) return FALSE;

        try {

            $client = new Client("ws://" . config('server_api.rcon_ip', '') . "/" . config('server_api.rcon_passw', ''));

            if (!$client) return FALSE;

            $data = json_encode([
                'Identifier' => 0,
                'Message'    => $command,
                'Stacktrace' => '',
                'Type'       => 3,
            ]);

            $client->send($data);
            $result = json_decode($client->receive());

            return $result;

        } catch (\Exception $ex) {
            //
        }

        return FALSE;
    }
}
