<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Calendar Service Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls which calendar service the application uses.
    | You can switch between local and external services.
    |
    */

    'service' => env('CALENDAR_SERVICE', 'local'),

    /*
    |--------------------------------------------------------------------------
    | External Calendar API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the external calendar API service.
    |
    */

    'external' => [
        'base_url' => env('CALENDAR_API_BASE_URL', 'https://pnldev.com/api/calender'),
        'timeout' => env('CALENDAR_API_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Local Calendar Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the local calendar service.
    |
    */

    'local' => [
        'cache_duration' => env('LOCAL_CALENDAR_CACHE_DURATION', 3600), // 1 hour
    ],
];