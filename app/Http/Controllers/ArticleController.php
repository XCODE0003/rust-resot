<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\ArticlesCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
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

    public function index(Request $request)
    {
        $page = request()->query('page');
        if (isset(auth()->user()->role) && auth()->user()->role == 'admin') {
            $articles = Article::query()->latest()->paginate(10);
        } else {
            $articles = Cache::remember('page_news'.$page, '600', function () {
                return Article::where('status', 1)->latest()->paginate(10);
            });
        }

        return view('pages.main.news.list', compact('articles'));
    }


    public function show($path)
    {
        $article = Cache::remember('article' . $path, '600', function () use($path) {
            return Article::where('path', $path)->first();
        });

        if(!$article) {
            abort('404');
        }

        if ( (!isset(auth()->user()->role) || auth()->user()->role != 'admin') && $article->status == 0) {
            abort('404');
        }

        return view('pages.main.news.full', compact('article'));
    }

    public function news_test()
    {
        $article = [];
        return view('pages.main.news.test', compact('article'));
    }

}
