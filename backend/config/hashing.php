<?php

return [
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'email' => 'email',
            'token_column' => 'remember_token',
        ],
    ],

    'limiter' => 'default',
];
