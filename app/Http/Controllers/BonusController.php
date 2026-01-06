<?php

namespace App\Http\Controllers;

use App\Models\WonItem;
use App\Models\Inventory;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BonusController extends Controller
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
        if(!isset(auth()->user()->role) || auth()->user()->role != 'admin') {
            abort(404);
        }

        $items = [];
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonus_pecul_'.$i.'_title', '') != '') {

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonus_pecul_'.$i.'_available', 0) > 0) {
                    $items_left += config('options.bonus_pecul_'.$i.'_available', 0);
                }

                $items[] = (object) [
                    'id' => $i,
                    'context' => (object) [
                        'name' => config('options.bonus_pecul_'.$i.'_title', ''),
                        'icon' => '/storage/' . config('options.bonus_pecul_'.$i.'_image', ''),
                        'quality_type' => config('options.bonus_pecul_'.$i.'_description', 1),
                    ],
                ];
            }
        }

        return view('pages.cabinet.bonus', compact('items', 'items_left'));
    }

    public function getBonusItemsForRoll(Request $request)
    {
        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonus_pecul_'.$i.'_title', '') != '') {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonus_pecul_'.$i.'_title', ''))->name,
                        'icon' => '/storage/' . config('options.bonus_pecul_'.$i.'_image', ''),
                    ],
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'ok',
            'result' => $items,
        ]);
    }

    public function open(Request $request)
    {
        abort(404);

        if (!isset(auth()->user()->online_time) || getHoursAmount(auth()->user()->online_time) < config('options.bonus_online_amount', '100')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        //проверяем и если это первый запрос, то записываем блок в сессию
        //Session::forget('bonus_open_' .auth()->id());

        if (Session::has('bonus_open_' .auth()->id())) {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not open bonus case due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        Session::put('bonus_open_' .auth()->id(), 1);
        Session::save();


        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonus_pecul_'.$i.'_title', '') != '' && config('options.bonus_pecul_'.$i.'_available', 0) > 0) {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonus_pecul_'.$i.'_title', ''))->name,
                        'chance' => config('options.bonus_pecul_'.$i.'_chance', 1),
                        'icon' => '/storage/' . config('options.bonus_pecul_'.$i.'_image', ''),
                        'skin_id' => config('options.bonus_pecul_'.$i.'_title', ''),
                    ],
                ];
            }
        }

        //Расчитываем выигранный предмет
        $win_index = get_random_item($items);
        if (isset($items[$win_index])) {
            $win_item = $items[$win_index];
        } else {
            $win_item = $items[0];
        }

        //Сбрасываем время онлайн
        auth()->user()->online_time = 0;
        auth()->user()->save();

        //Записываем выигрыш
        WonItem::create([
            'user_id'   => auth()->user()->id,
            'item'      => $win_item['context']['name'],
            'item_id'   => $win_item['context']['skin_id'],
            'item_icon' => $win_item['context']['icon'],
            'server'    => 'All',
            'issued'    => 0,
        ]);

        //Отнимаем кол-во предмета
        $available = intval(config('options.bonus_pecul_'.$win_item['id'].'_available', 0)) - 1;
        Option::updateOrCreate(['key' => 'bonus_pecul_'.$win_item['id'].'_available'], [
            'value' => $available,
            'default_key' => null
        ]);

        Cache::forget('options');

        //Добавляем выигрыш в инвентарь
        $inventory_item = [
            'type' => $win_item['context']['quality_type'],
            'image' => $win_item['context']['image'],
            'item_id' => $win_item['context']['skin_id'],
        ];
        Inventory::create([
            'type' => 0,
            'item' => json_encode($inventory_item),
            'amount' => 1,
            'user_id' => auth()->id(),
            'vip_period' => 0,
            'deposit_bonus' => 0,
            'balance' => 0,
        ]);


        Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item['context']['skin_id'].') from a Bonus case');

        Session::forget('bonus_open_' .auth()->id());

        return response()->json([
            'status' => 'success',
            'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
            'win_index' => $win_item['id'],
            'win_item' => $win_item,
        ]);
    }

    public function indexMonday()
    {
        if(!isset(auth()->user()->role) || auth()->user()->role != 'admin') {
            abort(404);
        }

        $items = [];
        $items_bd = [];
        $items_tmp = [];
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonusm_pecul_'.$i.'_title', '') != '') {

                //Собираем для сортировки по типу предмета
                $items_tmp[] = [
                    'id' => $i,
                    'quality_type' => config('options.bonusm_pecul_'.$i.'_description', 1),
                ];

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonusm_pecul_'.$i.'_available', 0) > 0) {
                    $items_left += config('options.bonusm_pecul_'.$i.'_available', 0);
                }

                $items_bd[] = (object) [
                    'id' => $i,
                    'context' => (object) [
                        'name' => config('options.bonusm_pecul_'.$i.'_title', ''),
                        'icon' => '/storage/' . config('options.bonusm_pecul_'.$i.'_image', ''),
                        'quality_type' => config('options.bonusm_pecul_'.$i.'_description', 1),
                    ],
                ];
            }
        }

        //Сортируем по типу предмета
        usort($items_tmp, "sort_bonus_type");
        foreach ($items_tmp as $item_tmp) {
            foreach ($items_bd as $item_bd) {
                if ($item_tmp['id'] == $item_bd->id) {
                    $items[] = $item_bd;
                }
            }
        }

        return view('pages.cabinet.bonus_monday', compact('items', 'items_left'));
    }

    public function getBonusItemsForRollMonday(Request $request)
    {
        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonusm_pecul_'.$i.'_title', '') != '') {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonusm_pecul_'.$i.'_title', ''))->name,
                        'icon' => '/storage/' . config('options.bonusm_pecul_'.$i.'_image', ''),
                    ],
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'ok',
            'result' => $items,
        ]);
    }

    public function openMonday(Request $request)
    {
        abort(404);

        if (!isset(auth()->user()->online_time_monday) || getHoursAmount(auth()->user()->online_time_monday) < config('options.bonusm_online_amount', '100')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonusm_pecul_'.$i.'_title', '') != '' && config('options.bonusm_pecul_'.$i.'_available', 0) > 0) {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonusm_pecul_'.$i.'_title', ''))->name,
                        'chance' => config('options.bonusm_pecul_'.$i.'_chance', 1),
                        'icon' => '/storage/' . config('options.bonusm_pecul_'.$i.'_image', ''),
                        'skin_id' => config('options.bonusm_pecul_'.$i.'_title', ''),
                    ],
                ];
            }
        }

        //Расчитываем выигранный предмет
        $win_index = get_random_item($items);
        if (isset($items[$win_index])) {
            $win_item = $items[$win_index];
        } else {
            $win_item = $items[0];
        }

        //Сбрасываем время онлайн
        auth()->user()->online_time_monday = 0;
        auth()->user()->save();

        //Записываем выигрыш
        WonItem::create([
            'user_id'   => auth()->user()->id,
            'item'      => $win_item['context']['name'],
            'item_id'   => $win_item['context']['skin_id'],
            'item_icon' => $win_item['context']['icon'],
            'server'    => 'EU Monday',
            'issued'    => 0,
        ]);

        //Отнимаем кол-во предмета
        $available = intval(config('options.bonusm_pecul_'.$win_item['id'].'_available', 0)) - 1;
        Option::updateOrCreate(['key' => 'bonusm_pecul_'.$win_item['id'].'_available'], [
            'value' => $available,
            'default_key' => null
        ]);

        Cache::forget('options');

        Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item['context']['skin_id'].') from a Bonus case on EU Monday Server');

        return response()->json([
            'status' => 'success',
            'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
            'win_index' => $win_item['id'],
            'win_item' => $win_item,
        ]);
    }

    public function indexThursday()
    {
        if(!isset(auth()->user()->role) || auth()->user()->role != 'admin') {
            abort(404);
        }

        $items = [];
        $items_bd = [];
        $items_tmp = [];
        $items_left = 0;
        for($i=0;$i<200;$i++) {
            if (config('options.bonusth_pecul_'.$i.'_title', '') != '') {

                //Собираем для сортировки по типу предмета
                $items_tmp[] = [
                    'id' => $i,
                    'quality_type' => config('options.bonusth_pecul_'.$i.'_description', 1),
                ];

                //Получаем общее кол-во сколько осталось доступно предметов-призов
                if (config('options.bonusth_pecul_'.$i.'_available', 0) > 0) {
                    $items_left += config('options.bonusth_pecul_'.$i.'_available', 0);
                }

                $items_bd[] = (object) [
                    'id' => $i,
                    'context' => (object) [
                        'name' => config('options.bonusth_pecul_'.$i.'_title', ''),
                        'icon' => '/storage/' . config('options.bonusth_pecul_'.$i.'_image', ''),
                        'quality_type' => config('options.bonusth_pecul_'.$i.'_description', 1),
                    ],
                ];
            }
        }

        //Сортируем по типу предмета
        usort($items_tmp, "sort_bonus_type");
        foreach ($items_tmp as $item_tmp) {
            foreach ($items_bd as $item_bd) {
                if ($item_tmp['id'] == $item_bd->id) {
                    $items[] = $item_bd;
                }
            }
        }

        return view('pages.cabinet.bonus_thursday', compact('items', 'items_left'));
    }

    public function getBonusItemsForRollThursday(Request $request)
    {
        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonusth_pecul_'.$i.'_title', '') != '') {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonusth_pecul_'.$i.'_title', ''))->name,
                        'icon' => '/storage/' . config('options.bonusth_pecul_'.$i.'_image', ''),
                    ],
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'ok',
            'result' => $items,
        ]);
    }

    public function openThursday(Request $request)
    {
        abort(404);

        if (!isset(auth()->user()->online_time_thursday) || getHoursAmount(auth()->user()->online_time_thursday) < config('options.bonusth_online_amount', '100')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonusth_pecul_'.$i.'_title', '') != '' && config('options.bonusth_pecul_'.$i.'_available', 0) > 0) {
                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => get_skin(config('options.bonusth_pecul_'.$i.'_title', ''))->name,
                        'chance' => config('options.bonusth_pecul_'.$i.'_chance', 1),
                        'icon' => '/storage/' . config('options.bonusth_pecul_'.$i.'_image', ''),
                        'skin_id' => config('options.bonusth_pecul_'.$i.'_title', ''),
                    ],
                ];
            }
        }

        //Расчитываем выигранный предмет
        $win_index = get_random_item($items);
        if (isset($items[$win_index])) {
            $win_item = $items[$win_index];
        } else {
            $win_item = $items[0];
        }

        //Сбрасываем время онлайн
        auth()->user()->online_time_thursday = 0;
        auth()->user()->save();

        //Записываем выигрыш
        WonItem::create([
            'user_id'   => auth()->user()->id,
            'item'      => $win_item['context']['name'],
            'item_id'   => $win_item['context']['skin_id'],
            'item_icon' => $win_item['context']['icon'],
            'server'    => 'Thursday',
            'issued'    => 0,
        ]);

        //Отнимаем кол-во предмета
        $available = intval(config('options.bonusth_pecul_'.$win_item['id'].'_available', 0)) - 1;
        Option::updateOrCreate(['key' => 'bonusth_pecul_'.$win_item['id'].'_available'], [
            'value' => $available,
            'default_key' => null
        ]);

        Cache::forget('options');

        Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item['context']['skin_id'].') from a Bonus case on Thursday Server');

        return response()->json([
            'status' => 'success',
            'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
            'win_index' => $win_item['id'],
            'win_item' => $win_item,
        ]);
    }
}
