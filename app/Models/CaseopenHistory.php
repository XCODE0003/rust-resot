<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CaseopenHistory extends Model
{

    public $timestamps = false;

    protected $fillable = [
      'case_id', 'user_id', 'item_id', 'item_amount', 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function case()
    {
        return $this->belongsTo(Cases::class);
    }
    public function getitem()
    {
        return ShopItem::where('id', $this->item_id)->first();
    }
}
