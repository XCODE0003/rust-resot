<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyPromoApiKey
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
        $apiKey = $request->header('X-Api-Key');
        $validKey = env('PROMO_API_KEY');

        if ($validKey === 'none') {
            return $next($request);
        }

        if (empty($validKey)) {
            return response()->json(['error' => 'API key not configured'], 500);
        }

        if (empty($apiKey) || $apiKey !== $validKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
