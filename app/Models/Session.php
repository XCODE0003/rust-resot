<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpbrowscap\Browscap;
use WhichBrowser\Parser;

class Session extends Model
{
    public $timestamps = false;
    protected $casts = [
        'id' => 'string'
    ];
    /**
     * @throws \phpbrowscap\Exception
     */
    public function getOs() {
        if ($this->os === null) {
            $result = new Parser($this->user_agent);
            $this->os = $result->os->name;
            $this->save();
        }
        return $this->os;
    }

    /**
     * @throws \phpbrowscap\Exception
     */
    public function getBrowser() {
        if ($this->browser === null) {
            $result = new Parser($this->user_agent);
            $this->browser = $result->browser->name;
            $this->save();
        }
        return $this->browser;
    }
}
