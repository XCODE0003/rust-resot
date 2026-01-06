<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServerCategory extends Model
{
    protected $table = 'servers_categories';

    protected $fillable = [
      'path', 'status', 'sort',
      'title_ru', 'title_en', 'title_de', 'title_fr', 'title_it', 'title_es', 'title_uk',
      'description_en', 'description_ru', 'description_de', 'description_fr', 'description_it', 'description_es', 'description_uk'
    ];
}