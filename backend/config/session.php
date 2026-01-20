<?php

return [
    'default' => env('SESSION_DRIVER', 'cookie'),

    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => env('SESSION_ENCRYPT', false),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        strtolower(str_replace(' ', '_', env('APP_NAME', 'Laravel'))) . '_session'
    ),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIES'),
    'http_only' => true,
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => false,
];
