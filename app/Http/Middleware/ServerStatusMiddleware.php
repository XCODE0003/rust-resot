<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServerStatusMiddleware
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
        return $next($request);

        /*
        if (server_status(session('server_id')) === 'Online') {
            return $next($request);
        }

        return redirect()->route('server.offline');
        */
    }
}
