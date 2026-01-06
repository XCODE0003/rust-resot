<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\ServerCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GameServer;

class ServersController extends Controller
{
    public function __construct()
    {
        $this->middleware('server.status');
    }

    public function index() {

        $servers = [];
        foreach (getservercategories() as $category) {
            $servers[$category->id] = [];
        }
        //Cache::forget('page_servers_items');
        $servers_items = Cache::remember('page_servers_items', '600', function () {
            return Server::where('status', 1)->orderBy('sort')->get();
        });

        foreach ($servers_items as $servers_item) {
            $servers[$servers_item->category_id][] = $servers_item;
            $servers['8'][] = $servers_item;
        }


        return view('pages.main.servers', compact('servers'));
    }

}
