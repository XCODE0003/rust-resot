<?php

namespace App\View\Components\Main;

use App\Models\Article;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class News extends Component
{
    public $articles;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (isset(auth()->user()->role) && auth()->user()->role == 'admin') {
            $this->articles = Article::where('type', 'news')->latest()->orderBy('sort')->limit(6)->get();
        } else {
            $this->articles = Cache::remember('news', '600', function () {
                return Article::where('type', 'news')->where('status', 1)->latest()->orderBy('sort')->limit(6)->get();
            });
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.news');
    }
}
