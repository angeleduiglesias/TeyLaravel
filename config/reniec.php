<?php

return [
    'reniec_api' => [
        'base_url' => env('RENIEC_API_BASE_URL', 'https://api.reniec.gob.pe'),
        'api_key' => env('RENIEC_API_KEY', ''),
        'timeout' => env('RENIEC_API_TIMEOUT', 10), // Timeout in seconds
    ],
];