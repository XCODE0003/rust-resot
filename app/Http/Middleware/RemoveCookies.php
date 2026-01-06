<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RemoveCookies
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            //config(['session.driver' => 'array']); // Устанавливаем драйвер сессии в 'array' для гостей
        }

        $response = $next($request);

        // Удаляем все куки из ответа
        $response->headers->remove('Set-Cookie');

        return $response;
    }
}

