<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\Request;

class Backend extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('backend.login');
        }
    }

    public function handle(Request $request, Closure $next)
    {
        if (!isset(auth()->user()->role) || auth()->user()->role == 'user') {
            abort(404);
        }
        return $next($request);
    }
}

