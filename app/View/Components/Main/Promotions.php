<?php

namespace App\View\Components\Main;

use App\Models\Article;
use Illuminate\View\Component;

class Promotions extends Component
{
    public $articles;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->articles = Article::latest()->where('type', 'promotions')->limit(10)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.promotions');
    }
}
