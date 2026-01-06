<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\GuideRequest;
use App\Models\Guide;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GuideController extends Controller
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
        $guides = Guide::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $guides->where('title_'.app()->getLocale(), 'LIKE', "%{$search}%");
        }

        $category_id = $request->has('cat') ? $request->get('cat') : '0';
        if ($category_id > 0) {
            $guides->where('category_id', $category_id);
        }

        if ($request->has('date') && $request->get('date') != '') {
            $guides->where('created_at', '>', $request->get('date').' 00:00:00')->where('created_at', '<', $request->get('date').' 23:59:59');
        }

        $guides = $guides->latest()->paginate();

        return view('backend.pages.guides.index', compact('guides','category_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.guides.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GuideRequest $request): RedirectResponse
    {
        $data = $request->validated();

        //Автозамена пробела в path на подчеркивание
        $data['path'] = str_replace(' ', '_', $data['path']);

        if ($data['title_en'] == '') $data['title_en'] = $data['title_ru'];
        if ($data['description_en'] == '') $data['description_en'] = $data['description_ru'];
        if ($data['meta_title_en'] == '') $data['meta_title_en'] = $data['meta_title_ru'];
        if ($data['meta_description_en'] == '') $data['meta_description_en'] = $data['meta_description_ru'];
        if ($data['meta_keywords_en'] == '') $data['meta_keywords_en'] = $data['meta_keywords_ru'];
        if ($data['meta_h1_en'] == '') $data['meta_h1_en'] = $data['meta_h1_ru'];
        if ($data['meta_h2_en'] == '') $data['meta_h2_en'] = $data['meta_h2_ru'];
        if ($data['meta_h3_en'] == '') $data['meta_h3_en'] = $data['meta_h3_ru'];

        $data['image'] = $request->image->store('images', 'public');
        Guide::create($data);

        $this->alert('success', __('Гайд успешно добавлен'));
        return redirect()->route('guides.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guide $guide)
    {
        return view('backend.pages.guides.form', compact('guide'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GuideRequest $request, Guide $guide): RedirectResponse
    {
        $data = $request->validated();

        //Автозамена пробела в path на подчеркивание
        $data['path'] = str_replace(' ', '_', $data['path']);

        if ($data['title_en'] == '') $data['title_en'] = $data['title_ru'];
        if ($data['description_en'] == '') $data['description_en'] = $data['description_ru'];
        if ($data['meta_title_en'] == '') $data['meta_title_en'] = $data['meta_title_ru'];
        if ($data['meta_description_en'] == '') $data['meta_description_en'] = $data['meta_description_ru'];
        if ($data['meta_keywords_en'] == '') $data['meta_keywords_en'] = $data['meta_keywords_ru'];
        if ($data['meta_h1_en'] == '') $data['meta_h1_en'] = $data['meta_h1_ru'];
        if ($data['meta_h2_en'] == '') $data['meta_h2_en'] = $data['meta_h2_ru'];
        if ($data['meta_h3_en'] == '') $data['meta_h3_en'] = $data['meta_h3_ru'];

        if (isset($data['image'])) {
            Storage::disk('public')->delete($guide->image);
            $data['image'] = $request->image->store('images', 'public');
        }

        $guide->update($data);

        $this->alert('success', __('Гайд успешно обновлен'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guide $guide)
    {
        Storage::disk('public')->delete($guide->image);
        $guide->delete();

        $this->alert('success', __('Гайд успешно удален'));
        return back();
    }
}
