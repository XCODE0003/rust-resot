<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Option;
use App\Models\Server;
use App\Services\Statistics;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class UpdateitemsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index() {
        $items = DB::connection('items_tmp')->table('item_name_tables')->get();

        $i = 0;
        foreach ($items as $item) {
            $i++;
            //if ($i > 3) break;
            DB::table('lineage_items')->where('id', $item->item_id)->update(['name' => trim($item->item_name)]);
            var_dump($item);
        }
    }

}
