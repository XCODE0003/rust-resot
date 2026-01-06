<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
      'image', 'type', 'path', 'status', 'sort',
      'title_en', 'description_en', 'meta_title_en', 'meta_description_en', 'meta_keywords_en', 'meta_h1_en', 'meta_h2_en', 'meta_h3_en',
      'title_ru', 'description_ru', 'meta_title_ru', 'meta_description_ru', 'meta_keywords_ru', 'meta_h1_ru', 'meta_h2_ru', 'meta_h3_ru',
      'title_de', 'description_de', 'meta_title_de', 'meta_description_de', 'meta_keywords_de', 'meta_h1_de', 'meta_h2_de', 'meta_h3_de',
      'title_fr', 'description_fr', 'meta_title_fr', 'meta_description_fr', 'meta_keywords_fr', 'meta_h1_fr', 'meta_h2_fr', 'meta_h3_fr',
      'title_it', 'description_it', 'meta_title_it', 'meta_description_it', 'meta_keywords_it', 'meta_h1_it', 'meta_h2_it', 'meta_h3_it',
      'title_es', 'description_es', 'meta_title_es', 'meta_description_es', 'meta_keywords_es', 'meta_h1_es', 'meta_h2_es', 'meta_h3_es',
      'title_uk', 'description_uk', 'meta_title_uk', 'meta_description_uk', 'meta_keywords_uk', 'meta_h1_uk', 'meta_h2_uk', 'meta_h3_uk',
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image);
    }

}
