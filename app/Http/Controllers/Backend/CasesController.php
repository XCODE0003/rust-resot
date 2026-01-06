<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CasesRequest;
use App\Models\Cases;
use App\Models\ShopItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;


class CasesController extends Controller
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
    public function index()
    {
        $cases = Cases::query()->where('kind', '!=', 2);

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $cases->where('title_' . app()->getLocale(), 'LIKE', "%{$search}%");
        }

        $cases = $cases->latest()->paginate();

        return view('backend.pages.cases.index', compact('cases'));
    }

    public function shop_list()
    {
        $cases = Cases::query()->where('kind', 2);

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $cases->where('title_' . app()->getLocale(), 'LIKE', "%{$search}%");
        }

        $cases = $cases->latest()->paginate();

        return view('backend.pages.cases.index', compact('cases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = [];
        $shopitems = ShopItem::query()->get();

        return view('backend.pages.cases.form', compact('items', 'shopitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CasesRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['image'] = $request->image->store('images', 'public');

        //Собираем предметы кейса
        $items = [];
        for ($i=0;$i<200;$i++) {
            $item_image = '';
            $var = 'case_item_'.$i.'_var';
            $item_id = 'case_item_'.$i.'_id';
            $type = 'case_item_'.$i.'_v';
            $drop_percent = 'case_item_'.$i.'_drop_percent';
            $available = 'case_item_'.$i.'_available';
            $image = 'case_item_'.$i.'_image';
            $vip_period = 'case_item_'.$i.'_vip_period';
            $deposit_bonus = 'case_item_'.$i.'_deposit_bonus';
            $balance = 'case_item_'.$i.'_balance';
            $shop_id = 'case_item_'.$i.'_shop_id';
            $shop_var = 'case_item_'.$i.'_shop_var';

            //Собираем диапазон кол-ва товара
            $shop_min = 'case_item_'.$i.'_shop_min';
            $shop_max = 'case_item_'.$i.'_shop_max';

            if ($request->has($item_id) && $request->has($drop_percent)) {

                if ($request->has($image) && is_file($request->$image)) {
                    $item_image = $request->$image->store('images', 'public');
                } else {
                    if ($request->input($var) == 1) {
                        $item_image = 'images/vip.png';
                    } else if ($request->input($var) == 2) {
                        $item_image = 'images/coupons/balance_percent_'.$request->input($deposit_bonus).'.png';
                    } else if ($request->input($var) == 3) {
                        $item_image = 'images/coupons/balance_'.$request->input($balance).'.png';
                    } else if ($request->input($var) == 5) {
                        $shopitem = ShopItem::find($request->input($shop_id));
                        if ($shopitem) {
                            $item_image = $shopitem->image;
                        }
                    } else {
                        $item_image = $request->$image;
                    }
                }

                //Собираем диапазон кол-ва товара
                if ($request->has($shop_min) && $request->has($shop_max) && $request->input($shop_min) > 1 && $request->input($shop_max) > 1) {
                    $shop_var_value = $request->input($shop_min) . '-' . $request->input($shop_max);
                } else {
                    $shop_var_value = $request->input($shop_var);
                }

                $items[] = [
                    'var'           => $request->input($var),
                    'item_id'       => ($request->input($var) == 5) ? 0 : $request->input($item_id),
                    'type'          => $request->input($type),
                    'drop_percent'  => $request->input($drop_percent),
                    'available'     => $request->input($available),
                    'image'         => $item_image,
                    'vip_period'    => $request->input($vip_period),
                    'deposit_bonus' => $request->input($deposit_bonus),
                    'balance'       => $request->input($balance),
                    'shop_id'       => $request->input($shop_id),
                    'shop_var'      => $shop_var_value,
                ];
            }
        }
        $data['items'] = json_encode($items);


        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'case_server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);
            }
        }
        $data['servers'] = json_encode($servers);

        Cases::create($data);

        $this->alert('success', __('Кейс успешно добавлен'));

        if ($data['kind'] == 2) {
            return redirect()->route('cases.shop_list');
        } else {
            return redirect()->route('cases.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cases $case)
    {
        $items = json_decode($case->items);
        $servers = json_decode($case->servers);

        $shopitems = ShopItem::query()->get();

        return view('backend.pages.cases.form', compact('case', 'items', 'shopitems', 'servers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function duplicate(Cases $case)
    {
        // Создаем копию
        $copiedCase = $case->replicate();
        $copiedCase->status = 0;
        $copiedCase->save();

        $case = $copiedCase;

        $items = json_decode($case->items);
        $servers = json_decode($case->servers);

        $shopitems = ShopItem::query()->get();

        return view('backend.pages.cases.form', compact('case', 'items', 'shopitems', 'servers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CasesRequest $request, Cases $case): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            Storage::disk('public')->delete($case->image);
            $data['image'] = $request->image->store('images', 'public');
        }

        $items = [];
        for ($i=0;$i<100;$i++) {
            $var = 'case_item_'.$i.'_var';
            $item_id = 'case_item_'.$i.'_id';
            $type = 'case_item_'.$i.'_type';
            $drop_percent = 'case_item_'.$i.'_drop_percent';
            $available = 'case_item_'.$i.'_available';
            $image = 'case_item_'.$i.'_image';
            $vip_period = 'case_item_'.$i.'_vip_period';
            $deposit_bonus = 'case_item_'.$i.'_deposit_bonus';
            $balance = 'case_item_'.$i.'_balance';
            $shop_id = 'case_item_'.$i.'_shop_id';
            $shop_var = 'case_item_'.$i.'_shop_var';

            //Собираем диапазон кол-ва товара
            $shop_min = 'case_item_'.$i.'_shop_min';
            $shop_max = 'case_item_'.$i.'_shop_max';

            if ($i == 0) {
                continue;
            }

            if ($request->has($item_id) && $request->has($drop_percent)) {
                if ($request->has($image) && is_file($request->$image)) {
                    $item_image = $request->$image->store('images', 'public');
                } else {
                    if ($request->input($var) == 1) {
                        $item_image = 'images/vip.png';
                    } else if ($request->input($var) == 2) {
                        $item_image = 'images/coupons/balance_percent_'.$request->input($deposit_bonus).'.png';
                    } else if ($request->input($var) == 3) {
                        $item_image = 'images/coupons/balance_'.$request->input($balance).'.png';
                    } else if ($request->input($var) == 5) {
                        $shopitem = ShopItem::find($request->input($shop_id));
                        if ($shopitem) {
                            $item_image = $shopitem->image;
                        }
                    } else {
                        $item_image = $request->$image;
                    }
                }

                //Собираем диапазон кол-ва товара
                if ($request->has($shop_min) && $request->has($shop_max) && $request->input($shop_min) >= 1 && $request->input($shop_max) >= 1) {
                    $shop_var_value = $request->input($shop_min) . '-' . $request->input($shop_max);
                } else {
                    $shop_var_value = $request->input($shop_var);
                }

                $items[] = [
                    'var'           => $request->input($var),
                    'item_id'       => ($request->input($var) == 5) ? 0 : $request->input($item_id),
                    'type'          => $request->input($type),
                    'drop_percent'  => $request->input($drop_percent),
                    'available'     => $request->input($available),
                    'image'         => $item_image,
                    'vip_period'    => $request->input($vip_period),
                    'deposit_bonus' => $request->input($deposit_bonus),
                    'balance'       => $request->input($balance),
                    'shop_id'       => $request->input($shop_id),
                    'shop_var'      => $shop_var_value,
                ];
            }
        }
        //dd($items);

        $data['items'] = json_encode($items);

        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'case_server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);

            }
        }
        $data['servers'] = json_encode($servers);

        $case->update($data);

        $this->alert('success', __('Кейс успешно обновлен'));


        if ($data['kind'] == 2) {
            return redirect()->route('cases.shop_list');
        } else {
            return redirect()->route('cases.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cases $case)
    {
        Storage::disk('public')->delete($case->image);

        $case->delete();

        $this->alert('success', __('Кейс успешно удален'));

        if ($case->kind == 2) {
            return redirect()->route('cases.shop_list');
        } else {
            return redirect()->route('cases.index');
        }
    }

}