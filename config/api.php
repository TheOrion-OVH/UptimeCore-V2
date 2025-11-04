<?php

use Illuminate\Support\Facades\Route;

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    'rate_limit' => env('API_RATE_LIMIT', 60),
];

