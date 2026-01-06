<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function alert($type, $message) {
        $session = Session::get('alert.' . $type);
        $messages = array();
        if ($session) {
            $messages = $session;
        }
        array_push($messages, $message);
        Session::flash('alert.' . $type, $messages);
    }
}
