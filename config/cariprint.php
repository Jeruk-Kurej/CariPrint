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

    'survey' => [
        // Folder berisi survey-shops.csv dan survey-prices.csv untuk diimpor oleh SurveyShopSeeder.
        'directory' => database_path('data'),

        // Pesan WhatsApp untuk meminta harga dan jam buka. {tempat} diganti nama tempatnya.
        'whatsapp_message' => "Halo kak, saya mahasiswa Universitas Ciputra. Kami sedang membuat website CariPrint untuk membantu mahasiswa mencari tempat print di sekitar CitraLand (gratis, tidak ada pemesanan lewat website). Boleh minta info untuk {tempat}?\n1. Daftar harga: print hitam-putih dan warna (A4/F4/A3), fotokopi, jilid, laminating, scan.\n2. Jam buka Senin sampai Minggu.\n3. Kalau ada logo atau gambar kecil berwarna di dokumen, dihitung warna atau hitam-putih?\nKalau ada foto daftar harganya boleh dikirim. Terima kasih kak.",
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
