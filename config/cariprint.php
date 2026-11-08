<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin account
    |--------------------------------------------------------------------------
    |
    | Read by AdminSeeder. Secrets stay in .env and are never committed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Tunable limits
    |--------------------------------------------------------------------------
    |
    | Quotas, area bounds and file lifetime. Change values here, not in code.
    |
    */

    'ai' => [
        'quota' => [
            'guest' => 3,
            'user' => 15,
        ],
    ],

    'area' => [
        // Bounding box around CitraLand Surabaya (used to validate shop coordinates).
        'min_latitude' => -7.3000,
        'max_latitude' => -7.2700,
        'min_longitude' => 112.6200,
        'max_longitude' => 112.6600,
    ],

    'uploads' => [
        // Uploaded files are deleted automatically after this many hours.
        'ttl_hours' => 24,
    ],

    'directory' => [
        // Minutes before closing when a shop is shown as "Segera tutup".
        'closing_soon_minutes' => 30,
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
