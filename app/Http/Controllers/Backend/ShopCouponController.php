<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ShopCouponRequest;
use App\Models\ShopCoupon;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ShopCouponController extends Controller
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
        $shopcoupons = ShopCoupon::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $shopcoupons->where('title_', 'LIKE', "%{$search}%");
        }

        $shopcoupons = $shopcoupons->latest()->paginate();

        return view('backend.pages.shop.coupons.index', compact('shopcoupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.shop.coupons.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopCouponRequest $request): RedirectResponse
    {
        $data = $request->validated();

        //Checking, that the bonus item is in the item DB
        if (!getitem($data['item_1_id'])) {
            $request->session()->put('item_err_id', __('Неверный ID предмета!'));
            return back();
        }

        $items = [];
        $items[] = [
            'id' => $data['item_1_id'],
            'name' => $data['item_1_name'],
            'amount' => abs($data['item_1_amount']),
        ];
        $data['items'] = json_encode($items);

        ShopCoupon::create($data);

        $this->alert('success', __('Купон успешно добавлен'));
        return redirect()->route('shopcoupons.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShopCoupon $shopcoupon)
    {
        return view('backend.pages.shop.coupons.form', compact('shopcoupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopCouponRequest $request, ShopCoupon $shopcoupon): RedirectResponse
    {
        $data = $request->validated();

        //Checking, that the bonus item is in the item DB
        if (!getitem($data['item_1_id'])) {
            $request->session()->put('item_err_id', __('Неверный ID предмета!'));
            return back();
        }

        $items = [];
        $items[] = [
            'id' => $data['item_1_id'],
            'name' => $data['item_1_name'],
            'amount' => abs($data['item_1_amount']),
        ];
        $data['items'] = json_encode($items);
        $shopcoupon->update($data);

        $this->alert('success', __('Купон успешно обновлен'));
        return redirect()->route('shopcoupons.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopCoupon $shopcoupon)
    {
        $shopcoupon->delete();

        $this->alert('success', __('Купон успешно удален'));
        return redirect()->route('shopcoupons.index');
    }
}