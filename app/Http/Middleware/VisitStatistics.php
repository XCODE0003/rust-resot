<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitStatistics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $visit = new Visit;
        $visit->ip = $request->ip();
        $visit->router = $request->route()->uri;
        $visit->save();

        return $next($request);
    }
}
