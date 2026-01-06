<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'adminlog' => [
            'driver' => 'single',
            'path' => storage_path('logs/admin.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'paymentslog' => [
            'driver' => 'single',
            'path' => storage_path('logs/payments.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'qiwi' => [
            'driver' => 'single',
            'path' => storage_path('logs/qiwi.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'enot' => [
            'driver' => 'single',
            'path' => storage_path('logs/enot.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'freekassa' => [
            'driver' => 'single',
            'path' => storage_path('logs/freekassa.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'appcent' => [
            'driver' => 'single',
            'path' => storage_path('logs/appcent.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'primepayments' => [
            'driver' => 'single',
            'path' => storage_path('logs/primepayments.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'pagseguro' => [
            'driver' => 'single',
            'path' => storage_path('logs/pagseguro.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'paymentwall' => [
            'driver' => 'single',
            'path' => storage_path('logs/paymentwall.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'paypal' => [
            'driver' => 'single',
            'path' => storage_path('logs/paypal.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'tebex' => [
            'driver' => 'single',
            'path' => storage_path('logs/tebex.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'yookassa' => [
            'driver' => 'single',
            'path' => storage_path('logs/yookassa.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'unitpay' => [
            'driver' => 'single',
            'path' => storage_path('logs/unitpay.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'cryptocloud' => [
            'driver' => 'single',
            'path' => storage_path('logs/cryptocloud.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'paykeeper' => [
            'driver' => 'single',
            'path' => storage_path('logs/paykeeper.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'alfabank' => [
            'driver' => 'single',
            'path' => storage_path('logs/alfabank.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'skinspay' => [
            'driver' => 'single',
            'path' => storage_path('logs/skinspay.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'pally' => [
            'driver' => 'single',
            'path' => storage_path('logs/pally.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],
        'heleket' => [
            'driver' => 'single',
            'path' => storage_path('logs/heleket.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'api' => [
            'driver' => 'single',
            'path' => storage_path('logs/api.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'api_req' => [
            'driver' => 'single',
            'path' => storage_path('logs/api_req.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'server_queries' => [
            'driver' => 'single',
            'path' => storage_path('logs/server_queries.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'waxpeer_buy' => [
            'driver' => 'single',
            'path' => storage_path('logs/waxpeer_buy.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'skinsback_buy' => [
            'driver' => 'single',
            'path' => storage_path('logs/skinsback_buy.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'rcon' => [
            'driver' => 'single',
            'path' => storage_path('logs/rcon.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'rcon_master' => [
            'driver' => 'single',
            'path' => storage_path('logs/rcon_master.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'players_online' => [
            'driver' => 'single',
            'path' => storage_path('logs/players_online.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'schedule' => [
            'driver' => 'single',
            'path' => storage_path('logs/schedule.log'),
            'level' => env('LOG_LEVEL', 'info'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
