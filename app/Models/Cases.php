<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cases extends Model
{
    protected $table = 'cases';

    protected $fillable = [
      'category_id', 'items', 'server', 'servers', 'price', 'price_usd', 'status', 'kind', 'online_amount', 'prizes_max', 'image', 'sort', 'is_free',
      'title_en', 'title_ru', 'title_de', 'title_es', 'title_fr', 'title_it', 'title_uk',
      'subtitle_en', 'subtitle_ru', 'subtitle_de', 'subtitle_es', 'subtitle_fr', 'subtitle_it', 'subtitle_uk',
      'description_en', 'description_ru', 'description_de', 'description_es', 'description_fr', 'description_it', 'description_uk',
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }
}
