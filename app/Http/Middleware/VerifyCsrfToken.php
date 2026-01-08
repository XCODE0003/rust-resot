<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api/payments/notification/app',
        '/cabinet/donate/app',
        'login_steam',
        'en/login_steam',
        '/pally/success',
        '/pally/fail'
    ];
}
