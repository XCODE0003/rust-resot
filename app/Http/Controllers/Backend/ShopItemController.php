<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests\ShopItemRequest;
use App\Models\ShopItem;
use App\Models\ShopCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ShopItemController extends Controller
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
        $shopitems = ShopItem::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $shopitems->where('name_' . app()->getLocale(), 'LIKE', "%{$search}%");
        }
        $category_id = $request->has('category_id') ? $request->get('category_id') : '0';
        if ($category_id > 0) {
            $shopitems->where('category_id', $category_id);
        }

        $shopitems = $shopitems->orderBy('sort')->orderBy('id')->paginate();

        $shopcategories = ShopCategory::query()->get();

        return view('backend.pages.shop.index', compact('shopitems', 'shopcategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.shop.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($data['name_ru'] == '') $data['name_ru'] = $data['name_en'];
        $data['image'] = $request->image->store('images', 'public');

        $variations = [];
        for($i=1;$i<50;$i++) {
            $variation_id = 'variation_'.$i.'_id';
            $variation_name = 'variation_'.$i.'_name';
            $variation_price = 'variation_'.$i.'_price';
            $variation_price_usd = 'variation_'.$i.'_price_usd';
            if($request->has($variation_id)) {
                $variations[] = [
                    "variation_id" => $request->input($variation_id),
                    "variation_name" => $request->input($variation_name),
                    "variation_price" => $request->input($variation_price),
                    "variation_price_usd" => $request->input($variation_price_usd),
                ];
            }
        }

        if (empty($variations)) {
            $variations = [];
            for ($i = 1; $i < 50; $i++) {
                $quantity_id = 'quantity_' . $i . '_id';
                $quantity_amount = 'quantity_' . $i . '_amount';
                $quantity_price = 'quantity_' . $i . '_price';
                $quantity_price_usd = 'quantity_' . $i . '_price_usd';
                if ($request->has($quantity_id)) {
                    $variations[] = [
                        "quantity_id"     => $request->input($quantity_id),
                        "quantity_amount" => $request->input($quantity_amount),
                        "quantity_price"  => $request->input($quantity_price),
                        "quantity_price_usd" => $request->input($quantity_price_usd),
                    ];
                }
            }
        }

        $data['variations'] = json_encode($variations);

        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);
            }
        }
        $data['servers'] = json_encode($servers);

        ShopItem::create($data);

        $this->alert('success', __('Предмет успешно добавлен'));
        return redirect()->route('shopitems.index');
    }

    /**
     * Duplicate and Store a newly created resource in storage.
     */
    public function duplicate(ShopItem $shopitem)
    {
        $data = $shopitem->getOriginal();
        $shopitem = ShopItem::create($data);

        $this->alert('success', __('Предмет успешно дублирован'));
        return redirect()->route('shopitems.edit', $shopitem);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopItem $shopitem)
    {
        $servers = json_decode($shopitem->servers);
        return view('backend.pages.shop.form', compact('shopitem', 'servers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopItemRequest $request, ShopItem $shopitem): RedirectResponse
    {

        $data = $request->validated();
        if ($data['name_ru'] == '') $data['name_ru'] = $data['name_en'];

        if (isset($data['image'])) {
            //Storage::disk('public')->delete($shopitem->image);
            $data['image'] = $request->image->store('images', 'public');
        }

        $variations = [];
        for($i=1;$i<50;$i++) {
            $variation_id = 'variation_'.$i.'_id';
            $variation_name = 'variation_'.$i.'_name';
            $variation_price = 'variation_'.$i.'_price';
            $variation_price_usd = 'variation_'.$i.'_price_usd';
            if($request->has($variation_id)) {
                $variations[] = [
                    "variation_id" => $request->input($variation_id),
                    "variation_name" => $request->input($variation_name),
                    "variation_price" => $request->input($variation_price),
                    "variation_price_usd" => $request->input($variation_price_usd),
                ];
            }
        }

        if (empty($variations)) {
            $variations = [];
            for ($i = 1; $i < 50; $i++) {
                $quantity_id = 'quantity_' . $i . '_id';
                $quantity_amount = 'quantity_' . $i . '_amount';
                $quantity_price = 'quantity_' . $i . '_price';
                $quantity_price_usd = 'quantity_' . $i . '_price_usd';
                if ($request->has($quantity_id)) {
                    $variations[] = [
                        "quantity_id"     => $request->input($quantity_id),
                        "quantity_amount" => $request->input($quantity_amount),
                        "quantity_price"  => $request->input($quantity_price),
                        "quantity_price_usd" => $request->input($quantity_price_usd),
                    ];
                }
            }
        }

        $data['variations'] = json_encode($variations);

        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);
            }
        }
        $data['servers'] = json_encode($servers);

        $shopitem->update($data);

        $this->alert('success', __('Предмет успешно обновлен'));
        return redirect()->route('shopitems.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopItem $shopitem)
    {
        //Storage::disk('public')->delete($shopitem->image);

        $shopitem->delete();

        $this->alert('success', __('Предмет успешно удален'));
        return redirect()->route('shopitems.index');
    }

    public function getVariations(Request $request)
    {
        $shopitem = ShopItem::find($request->shopitem_id);

        if($shopitem && isset($shopitem->variations)) {
            $variations = json_decode($shopitem->variations);
        } else {
            $variations = [];
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'ok',
            'result' => $variations,
        ]);

    }

    public function resetCache()
    {
        foreach (getservers() as $server) {
            foreach (getshopcategories() as $shopcategory) {
                Cache::forget('page_shop_shopitems_cat'.$shopcategory->id.$server->id);
                Cache::forget('page_shop_shopsets_cat'.$shopcategory->id.$server->id);
            }
        }

        $this->alert('success', __('Кеш магазина успешно сброшен'));
        return back();
    }
}