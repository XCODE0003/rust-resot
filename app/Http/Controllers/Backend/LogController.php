<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Models\Server;
use App\Models\User;
use App\Models\ShopItem;
use App\Services\Statistics;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class LogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:investor');
    }

    /**
     * Index page with form update settings
     */
    public function index() {
        return view('backend.pages.logs.payments');
    }

    public function payments(Request $request, Statistics $statistics)
    {

        $user_id = $request->has('user_id') ? $request->get('user_id') : 0;
        $type = $request->has('type') ? $request->get('type') : 'month';
        $payment_system = $request->has('payment_system') && $request->get('payment_system') !== null ? $request->get('payment_system') : '';
        $server_id = $request->has('server_id') ? $request->get('server_id') : '0';
        $status = $request->has('status') ? $request->get('status') : '-1';
        $date_start = ($request->has('date_start') && $request->get('date_start') !== NULL) ? date('Y-m-d', strtotime($request->get('date_start'))) . ' 00:00:00' : '';
        $date_end = ($request->has('date_end') && $request->get('date_end') !== NULL) ? date('Y-m-d', strtotime($request->get('date_end'))) . ' 23:59:59' : '';
        $date_type = ($request->has('date_type') && $request->get('date_type') !== NULL) ? $request->get('date_type') : 1;

        $data = $statistics->getPayments($type, $server_id, $status, $date_start, $date_end, $date_type, $user_id, $payment_system);

        return view('backend.pages.logs.payments', compact('data'));
    }

    public function shop(Request $request, Statistics $statistics) {

        $type = $request->has('type') ? $request->get('type') : 'month';
        $server_id = $request->has('server_id') ? $request->get('server_id') : '0';
        $item_id = $request->has('item_id') ? $request->get('item_id') : '0';

        $shopitems = ShopItem::where('status', 1)->get();

        $data = $statistics->getShopPurchases($type, $server_id, $item_id);

        return view('backend.pages.logs.shop', compact('data', 'shopitems'));
    }

    public function visits(Request $request, Statistics $statistics) {

        $type = $request->has('type') ? $request->get('type') : 'month';

        $data = $statistics->getVisits($type);

        return view('backend.pages.logs.visits', compact('data'));
    }

    public function registrations(Request $request, Statistics $statistics) {

        $server_id = $request->has('server_id') ? $request->get('server_id') : '1';

        $data = $statistics->getRegistrations($server_id);

        return view('backend.pages.logs.registrations', compact('data'));
    }

    public function gamecurrencylogs() {
        $path = storage_path('logs/payments.log');

        if (!file_exists($path)) {
            abort(404, 'Log file not found');
        }

        $log = file_get_contents($path);
        return view('backend.pages.logs.gamecurrencylogs', compact('log'));
    }

    public function adminlogs() {
        $path = storage_path('logs/admin.log');

        if (!file_exists($path)) {
            abort(404, 'Log file not found');
        }

        $log = file_get_contents($path);
        return view('backend.pages.logs.adminlogs', compact('log'));
    }

    public function servererrors() {
        $path = storage_path('logs/laravel.log');

        if (!file_exists($path)) {
            abort(404, 'Log file not found');
        }

        $log = file_get_contents($path);
        return view('backend.pages.logs.servererrors', compact('log'));
    }

    public function userlogs(User $user) {

        $admin_log_path = storage_path('logs/admin.log');
        $admin_log = '';
        if (file_exists($admin_log_path)) {
            $admin_log_content = file_get_contents($admin_log_path);
            $admin_log_arr = explode("\n", $admin_log_content);
            if (!empty($admin_log_arr)) {
                $logs = '';
                foreach ($admin_log_arr as $log) {
                    if (strpos($log, $user->name) !== FALSE) {
                        $logs .= $log . "\n";
                    }
                }
                $admin_log = $logs;
            }
        }

        $payments_log_path = storage_path('logs/payments.log');
        $payments_log = '';
        if (file_exists($payments_log_path)) {
            $payments_log_content = file_get_contents($payments_log_path);
            $payments_log_arr = explode("\n", $payments_log_content);
            if (!empty($payments_log_arr)) {
                $logs = '';
                foreach ($payments_log_arr as $log) {
                    if (strpos($log, $user->name) !== FALSE || strpos($log, "Игроку ID: {$user->id}") !== FALSE  || strpos($log, '"user_id":'.$user->id) !== FALSE) {
                        $logs .= $log . "\n";
                    }
                }
                $payments_log = $logs;
            }
        }

        return view('backend.pages.logs.userlogs', compact('admin_log', 'payments_log'));
    }

    public function statistics_game_items(Request $request, Statistics $statistics) {

        $type = $request->has('type') ? $request->get('type') : 'all';
        $server_id = $request->has('server_id') ? $request->get('server_id') : '1';
        $item_id = $request->has('item_id') ? $request->get('item_id') : '57';

        $data = $statistics->getItems($type, $server_id, $item_id);

        return view('backend.pages.logs.statistics_game_items', compact('data'));
    }

}
