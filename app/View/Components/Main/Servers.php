<?php

namespace App\View\Components\Main;

use App\Models\Server;
use Illuminate\View\Component;

class Servers extends Component
{
    public $servers;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->servers = Server::where('status', '1')->orderBy('sort')->limit(5)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.servers');
    }
}
