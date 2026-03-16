<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $this->registerErrorViewPaths();

        if ($e instanceof PostTooLargeException) {
            Session::put('alert.danger', [__("Файл слишком большой!")]);
            return redirect()->back();
        }

        // Для AJAX-запросов всегда показываем детальную ошибку
        if ($request->expectsJson() || $request->ajax()) {
            $message = $e->getMessage();
            $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            return response()->json([
                'success' => false,
                'message' => $message ?: 'Server Error',
                'error' => $message,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], $code >= 400 ? $code : 500);
        }

        // Показывать сообщение ошибки в ответе (для отладки, отключить после)
        if (env('APP_SHOW_ERROR_IN_RESPONSE', false)) {
            $message = $e->getMessage();

            return response(
                '<h1>Ошибка</h1><pre>' . e($message) . '</pre>' .
                (config('app.debug') ? '<pre>' . e($e->getTraceAsString()) . '</pre>' : ''),
                500
            )->header('Content-Type', 'text/html; charset=utf-8');
        }

        return parent::render($request, $e);
    }
}
