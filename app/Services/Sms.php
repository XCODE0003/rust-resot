<?php

namespace App\Services;

use App\Models\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Sms
{

    public function __construct()
    {

    }

    public function send($phone, $message)
    {

        require base_path('vendor') . '/Redsms/RedsmsApiSimple.php';

        //echo 'REDSMS.RU sms send';

        $login = config('options.sms_login', "");
        $apiKey = config('options.sms_api_key', "");
        $smsSenderName = config('options.sms_sender_name', "");

        $redsmsApi = new \Redsms\RedsmsApiSimple($login, $apiKey);
        $lastMessageUuid = '';
        $result = array();

        try {
            $sendResult = $redsmsApi->sendSMS($phone, $message, $smsSenderName);
            if (!empty($sendResult['items']) && $messages = $sendResult['items'] ) {
                foreach ($messages as $message) {
                    echo $message['to'].':'.$message['uuid'].PHP_EOL;
                    $lastMessageUuid = $message['uuid'];
                }
            }

            if ($lastMessageUuid) {
                //echo 'Get message info: ';
                $redsmsApi->messageInfo($lastMessageUuid);
                //echo 'wait 10 sec... ';
                sleep(10);
                $redsmsApi->messageInfo($lastMessageUuid);
            }

        } catch (\Exception $e) {
            $result = array(
                "status" => "error",
                "code" => $e->getCode(),
                "message" => $e->getMessage(),
            );
            return $result;
        }

        $result = array(
            "status" => "success",
            "code" => "200",
            "message" => "complete",
        );
        return $result;
    }

}