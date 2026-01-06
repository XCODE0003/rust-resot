<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ShopSetRequest;
use App\Models\ShopSet;
use App\Models\ShopItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;


class ShopSetController extends Controller
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
        $shopsets = ShopSet::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $shopsets->where('title_', 'LIKE', "%{$search}%");
        }

        $shopsets = $shopsets->latest()->paginate();

        return view('backend.pages.shop.sets.index', compact('shopsets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = [];
        $shopitems = ShopItem::query()->where('is_command', 0)->get();
        return view('backend.pages.shop.sets.form', compact('items', 'shopitems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopSetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['image'] = $request->image->store('images', 'public');

        //Собираем предметы кейса
        $items = [];
        for ($i=0;$i<200;$i++) {
            $id = 'shop_item_'.$i.'_id';
            if ($request->has($id)) {
                $items[] = [
                    'id' => $request->input('shop_item_'.$i.'_id'),
                    'amount' => $request->input('shop_item_'.$i.'_amount'),
                ];
            }
        }
        $data['items'] = json_encode($items);

        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);
            }
        }
        $data['servers'] = json_encode($servers);

        ShopSet::create($data);

        $this->alert('success', __('Сет успешно добавлен'));
        return redirect()->route('shopsets.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopSet $shopset)
    {
        $items = json_decode($shopset->items);
        $servers = json_decode($shopset->servers);

        $shopitems = ShopItem::query()->where('is_command', 0)->get();

        return view('backend.pages.shop.sets.form', compact('shopset', 'items', 'shopitems', 'servers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopSetRequest $request, ShopSet $shopset): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            $data['image'] = $request->image->store('images', 'public');
        }

        //Собираем предметы кейса
        $items = [];
        for ($i=0;$i<200;$i++) {
            $id = 'shop_item_'.$i.'_id';
            if ($request->has($id)) {
                $items[] = [
                    'id' => $request->input('shop_item_'.$i.'_id'),
                    'amount' => $request->input('shop_item_'.$i.'_amount'),
                ];
            }
        }
        $data['items'] = json_encode($items);

        //Собираем сервера, с которых считать онлайн
        $servers = [];
        for ($i=0;$i<200;$i++) {
            $server_id = 'server_'.$i.'_id';
            if ($request->has($server_id)) {
                $servers[] = $request->input($server_id);
            }
        }
        $data['servers'] = json_encode($servers);

        $shopset->update($data);

        $this->alert('success', __('Сет успешно обновлен'));
        return redirect()->route('shopsets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopSet $shopset)
    {
        Storage::disk('public')->delete($shopset->image);

        $shopset->delete();

        $this->alert('success', __('Сет успешно удален'));
        return redirect()->route('shopsets.index');
    }
}