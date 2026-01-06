<?php

namespace App\View\Components\Main;

use Illuminate\View\Component;

class Rating extends Component
{
    public $players;
    public $clans;
    public $castles;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {


        $rating = server_rating();
       // $this->players = collect($rating->pvp);
       // $this->players->splice(5);
       //$this->clans = collect($rating->clan_pvp);
       // $this->clans->splice(5);
        $this->castles = collect($rating->castles);
        $this->castles->splice(5);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.rating');
    }
}
