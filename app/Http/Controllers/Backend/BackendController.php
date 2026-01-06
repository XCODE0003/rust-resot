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

class BackendController extends Controller
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

    public function index(Request $request, Statistics $statistics) {

        //Cache::forget('dashboard:payments');
        $data["statistics"] = Cache::remember('dashboard:statistics', '3600', function () {
            $statistics = new Statistics();
            return $statistics->getVisits('month');
        });
        $data["payments"] = Cache::remember('dashboard:payments', '3600', function () {
            $statistics = new Statistics();
            return $statistics->getPayments('month', '');
        });
        $data["registrations"] = Cache::remember('dashboard:registrations', '3600', function () {
            $statistics = new Statistics();
            return $statistics->getRegistrations('');
        });

        return view('backend.pages.dashboard', compact('data'));
    }

    public function admin(User $user): RedirectResponse
    {
        $user->role = 'admin';
        $user->save();
        $this->alert('success', __('Вы успешно назначили ') . $user->name . __('администратором'));
        return back();
    }

    public function support(User $user): RedirectResponse
    {
        $user->role = 'support';
        $user->save();
        $this->alert('success', __('Вы успешно назначили ') . $user->name . __('агентом поддержки'));
        return back();
    }

    public function user(User $user): RedirectResponse
    {
        $user->role = 'user';
        $user->save();
        $this->alert('success', __('Вы успешно назначили ') . $user->name . __('обычным пользователем'));
        return back();
    }
}
