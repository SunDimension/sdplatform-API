<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://192.168.1.51:8080',
        'http://localhost:8080',
        'http://127.0.0.1:8080',
        'http://127.0.0.1:8000',
        'http://localhost:3009',
        'http://192.168.1.100:8001',
        'http://192.168.1.100'

    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
