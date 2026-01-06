<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Faq extends Model
{
    protected $fillable = [
      'question_ru', 'question_en', 'question_de', 'question_fr', 'question_it', 'question_es', 'question_uk', 'answer_ru', 'answer_en', 'answer_de', 'answer_fr', 'answer_it', 'answer_es', 'answer_uk', 'sort'
    ];

}
