<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
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
        $banners = Banner::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $banners->where('path', 'LIKE', "%{$search}%");
        }

        $banners = $banners->latest()->paginate();

        return view('backend.pages.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.banners.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerRequest $request): RedirectResponse
    {
        $data['path'] = $request->path;

        $rules = array(
            'image' => 'image'
        );

        $banners = [];

        for($i=0;$i<100;$i++) {
            $image = 'baner_'.$i.'_image';
            $sort = 'baner_'.$i.'_sort';
            if($request->has($image)) {

                $input = array('image' => $request->$image);
                $validator = Validator::make($input, $rules);
                if (!$validator->fails()) {
                    $banners[] = [
                        'image' => $request->$image->store('images', 'public'),
                        'sort'  => $request->$sort,
                    ];
                }
            }
        }

        $data['banners'] = json_encode($banners);

        Banner::create($data);

        $this->alert('success', __('Баннер успешно добавлен'));
        return redirect()->route('banners.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('backend.pages.banners.form', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerRequest $request, Banner $banner): RedirectResponse
    {
        $data['path'] = $request->path;

        $rules = array(
            'image' => 'image'
        );


        $banners_old = json_decode($banner->banners);
        $banners = [];

        for($i=0;$i<100;$i++) {
            $image = 'baner_'.$i.'_image';
            $image_old = 'baner_'.$i.'_image_old';
            $sort = 'baner_'.$i.'_sort';
            if($request->has($sort)) {

                if ($request->has($image_old) && !$request->has($image)) {
                    $banners[] = [
                        'image' => $request->$image_old,
                        'sort'  => $request->$sort,
                    ];
                } else {
                    $input = array('image' => $request->$image);
                    $validator = Validator::make($input, $rules);
                    if (!$validator->fails()) {
                        $banners[] = [
                            'image' => $request->$image->store('images', 'public'),
                            'sort'  => $request->$sort,
                        ];
                    }
                }
            }
        }

        //Удаляем старые фото из баннера
        foreach ($banners_old as $banner_old) {
            $banner_old_find = FALSE;
            foreach ($banners as $banner_item) {
                if ($banner_old->image == $banner_item['image']) {
                    $banner_old_find = TRUE;
                }
            }
            if ($banner_old_find === FALSE) {
                Storage::disk('public')->delete($banner_old->image);
            }
        }

        $data['banners'] = json_encode($banners);

        $banner->update($data);

        $this->alert('success', __('Баннер успешно обновлен'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $banners_old = json_decode($banner->banners);
        //Удаляем старые фото из баннера
        foreach ($banners_old as $banner_old) {
            Storage::disk('public')->delete($banner_old->image);
        }

        $banner->delete();

        $this->alert('success', __('Баннер успешно удален'));
        return back();
    }
}
