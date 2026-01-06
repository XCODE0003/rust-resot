<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ShopItem;
use App\Models\ShopSet;
use App\Models\Warehouse;
use App\Models\ShopCoupon;
use App\Models\ShopCart;
use App\Models\User;
use App\Models\LineageItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GameServer;

class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('server.status');
    }

    public function index() {

        $data = [];
        return view('pages.main.help', compact('data'));

    }

}
