<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\PromoCodeRequest;
use App\Http\Requests\PromoCodeGenerateRequest;
use App\Http\Requests\PromoCodeTemplateRequest;
use App\Models\PromoCode;
use App\Models\Cases;
use App\Models\ShopItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $promocodes = PromoCode::query();

        $search = request()->query('search');
        $status = request()->query('status');

        if (request()->has('search') && is_string($search)) {
            $promocodes->where('code', 'LIKE', "%{$search}%");
        }

        if ($status && $status == '2') {
            $promocodes->where('type', 2)->where('users','!=', NULL);
        } else {
            $date = date('Y-m-d H:i', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 24);
            //$promocodes->where('type', '!=', 2)->orWhere('type', 2);
            $promocodes->where(function ($query) use ($date) {
                $query->where('type', '!=', 2)->orWhere(function ($query) use ($date) {
                    $query->where('type', 2)
                        ->where('updated_at', '>', $date);
                });
            });
        }

        $promocodes = $promocodes->latest()->paginate();

        //Задаем метку, что это генерация промокодов и нужно выгрузить список
        $promocodes_file = (session()->get('promocodes_file', '') != '') ? TRUE : FALSE;

        return view('backend.pages.promocodes.index', compact('promocodes', 'promocodes_file'));
    }

    public function show(PromoCode $promocode)
    {
        $used_count = 0;
        $used_users = json_decode($promocode->users);
        if ($used_users !== NULL || !empty($used_users)) {
            $used_count = count($used_users);
        }

        return view('backend.pages.promocodes.info', compact('promocode','used_users', 'used_count'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Получаем кейсы
        $cases = Cases::query()->get();
        $shopitems = ShopItem::where('status', 1)->get();

        return view('backend.pages.promocodes.form', compact('cases', 'shopitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromoCodeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $promocode = PromoCode::create($data);

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Created a promo code. Parameters: " . json_encode($promocode));

        $this->alert('success', __('Промокод успешно добавлен'));
        return redirect()->route('promocodes.index');
    }

    public function generate()
    {
        //Получаем кейсы и товары магазина
        $cases = Cases::query()->get();
        $shopitems = ShopItem::where('status', 1)->get();

        return view('backend.pages.promocodes.generate', compact( 'cases', 'shopitems'));
    }

    public function generate_store(PromoCodeGenerateRequest $request)
    {
        //Собираем награды (предметы, баланс, премиум), они одинаковые для всех промокодов
        $type_reward = $request->has('type_reward') ? $request->input('type_reward') : 0;
        $bonus_amount = $request->has('bonus_amount') ? $request->input('bonus_amount') : 0;
        $case_id = $request->has('case_id') ? $request->input('case_id') : 0;
        $premium_period = $request->has('premium_period') ? $request->input('premium_period') : 0;
        $shop_item_id = $request->has('shop_item_id') ? $request->input('shop_item_id') : 0;
        $variation_id = $request->has('variation_id') ? $request->input('variation_id') : 0;

        if ($request->has('amount')) {
            for($a=0; $a<$request->amount;) {

                //Проверяю по итогу, что промокода с таким кодом нет, иначе пропускаю создание.
                $code = generationPromoCode();
                if (PromoCode::where('code', $code)->first()) continue;

                $data = [
                    'title' => $request->title,
                    'code' => $code,
                    'type' => 2,
                    'user_id' => NULL,
                    'users' => NULL,
                    'type_reward' => $type_reward,
                    'bonus_amount' => $bonus_amount,
                    'case_id' => $case_id,
                    'bonus_case_id' => 0,
                    'premium_period' => $premium_period,
                    'shop_item_id' => $shop_item_id,
                    'variation_id' => $variation_id,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                    'items' => NULL,
                ];
                PromoCode::create($data);

                $a++;
            }
        }

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Generate a promo codes in amount ".$request->amount.". Parameters: " . json_encode($request->all()));
        $this->alert('success', __('Промокоды успешно добавлены'));

        return redirect()->route('promocodes.index');
    }

    public function template()
    {
        return view('backend.pages.promocodes.template');
    }

    public function template_store(PromoCodeTemplateRequest $request)
    {
        //Собираем награды за уровень Реферала
        switch ($request->template_type) {
            case 0:
                $lvl = 40;
                break;
            case 1:
                $lvl = 70;
                break;
            case 2:
                $lvl = 75;
                break;
            case 3:
                $lvl = 80;
                break;
            case 4:
                $lvl = 85;
                break;
            default:
                $lvl = 0;
        }
        $items = [];

        $i = 0;
        for($it=0;$it<500;$it++) {
            $lvl_reward = config('options.reflink_bitem_' . $i . '_' . $it . '_lvl', 0);
            if ($lvl == $lvl_reward) {
                $lineageitem = LineageItem::where('id', config('options.reflink_bitem_' . $i . '_' . $it . '_id', 1))->first();
                $name = ($lineageitem) ? $lineageitem->name : '';
                $items[] = [
                    'id' => config('options.reflink_bitem_' . $i . '_' . $it . '_id', 1),
                    "name" => $name,
                    'amount' => config('options.reflink_bitem_' . $i . '_' . $it . '_quantity', 1),
                ];
            }
        }

        $data = [
            'title'            => $request->title,
            'code'             => $request->code,
            'type'             => 2,
            'type_restriction' => 0,
            'user_id'          => NULL,
            'users'            => NULL,
            'type_reward'      => 0,
            'balance'          => 0,
            'premium_period'   => 0,
            'date_start'       => $request->date_start,
            'date_end'         => $request->date_end,
            'items'            => json_encode($items),
        ];

        PromoCode::create($data);

        $this->alert('success', __('Промокод успешно добавлен'));

        return redirect()->route('promocodes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promocode)
    {
        //Получаем кейсы и товары магазина
        $cases = Cases::query()->get();
        $shopitems = ShopItem::where('status', 1)->get();

        return view('backend.pages.promocodes.form', compact('promocode', 'cases', 'shopitems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromoCodeRequest $request, PromoCode $promocode): RedirectResponse
    {
        $data = $request->validated();

        $promocode->update($data);

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Update a promo code. Parameters: " . json_encode($promocode));

        $this->alert('success', __('Промокод успешно обновлен'));
        return redirect()->route('promocodes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promocode)
    {
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Delete a promo code. Parameters: " . json_encode($promocode));

        $promocode->delete();

        $this->alert('success', __('Промокод успешно удален'));
        return redirect()->route('promocodes.index');
    }
}