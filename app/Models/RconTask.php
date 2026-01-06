<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RconTask extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
      'server', 'command', 'status', 'comment'
    ];
}
