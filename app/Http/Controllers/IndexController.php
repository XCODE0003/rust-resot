<?php

namespace App\Http\Controllers;

use App\Lib\GameServer;
use App\Lib\SteamApi;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Account;
use App\Models\Characters;
use App\Models\Warehouse;
use App\Models\Server;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Mail;

class IndexController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function index()
    {
        return view('index');
    }

    public function test() {

        $email = 'admin@wizardcp.com';
        $password = '123456';
        $result = SteamApi::login($email, $password);
        return Redirect::to($result);

        if ($result !== FALSE) {
            setcookie($result->auth_key, $result->auth_token, time() + 2592000);
        }

        return view('index');
    }

}
