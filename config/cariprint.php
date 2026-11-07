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

    'directory' => [
        // Minutes before closing when a shop is shown as "Segera tutup".
        'closing_soon_minutes' => 30,
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
