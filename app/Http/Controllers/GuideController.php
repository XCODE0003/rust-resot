<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuideRequest;
use App\Models\Guide;
use App\Models\GuideCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $category_id = request()->query('category_id');
        $page = request()->query('page');
        if (request()->has('category_id') && is_string($category_id)) {
            if (isset(auth()->user()->role) && auth()->user()->role == 'admin') {
                $guides = Guide::query()->where('category_id', $category_id)->latest()->paginate(10);
            } else {
                $guides = Cache::remember('page_guides_category'.$category_id.$page, '600', function () use($category_id) {
                    return Guide::where('status', 1)->where('category_id', $category_id)->latest()->paginate(10);
                });
            }
        } else {
            if (isset(auth()->user()->role) && auth()->user()->role == 'admin') {
                $guides = Guide::query()->latest()->paginate(10);
            } else {
                $guides = Cache::remember('page_guides_category0'.$page, '600', function () {
                    return Guide::where('status', 1)->latest()->paginate(10);
                });
            }
        }

        return view('pages.main.guides.list', compact('guides'));
    }

    public function show($category_path, $guide_path)
    {
        $guide = Guide::where('path', $guide_path)->first();
        if(!$guide) {
            abort('404');
        }

        if ( (!isset(auth()->user()->role) || auth()->user()->role != 'admin') && $guide->status == 0) {
            abort('404');
        }

        return view('pages.main.guides.full', compact('guide'));
    }

}
