<?php

namespace App\View\Components\Main;

use App\Lib\GameForum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;


class Forum extends Component
{
    public $posts = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Cache::forget('forum:posts');
        $this->posts = Cache::remember('forum:posts', '3600', function () {
            return GameForum::getPosts();
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.forum');
    }
}
