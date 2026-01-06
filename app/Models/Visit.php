<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Visit extends Model
{
    protected $fillable = [
      'id', 'ip', 'router'
    ];
}
