<?php

namespace App\Http\Middleware;

use Closure;
use Artisan;

class CacheKiller
{
    public function handle($request, Closure $next)
    {

	Artisan::call('view:clear');

        return $next($request);
    }
}