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
        // Requests per day. Guests are counted per browser session; ip_ceiling only stops scripts that
        // clear cookies, and is high enough that a campus network sharing one IP never hits it. Admins are unlimited.
        'quota' => [
            'guest' => 3,
            'user' => 15,
            'ip_ceiling' => 60,
        ],

        // The quota resets at midnight in this timezone.
        'reset_timezone' => 'Asia/Jakarta',
    ],

    'area' => [
        // Bounding box around CitraLand Surabaya (used to validate shop coordinates).
        'min_latitude' => -7.3000,
        'max_latitude' => -7.2700,
        'min_longitude' => 112.6200,
        'max_longitude' => 112.6600,
    ],

    // Wider box around the whole city: the admin form accepts shops anywhere inside it (imported drafts included).
    'city_area' => [
        'min_latitude' => -7.4000,
        'max_latitude' => -7.1500,
        'min_longitude' => 112.5500,
        'max_longitude' => 112.8700,
    ],

    'import' => [
        // The admin may limit an Apify import to places within this many kilometres of CitraLand (its upper bound).
        'max_radius_km' => 30,
    ],

    // Halaman PDF dinilai dari bagian halaman yang berwarna (0 sampai 1). Sampai batas bawah dianggap hitam-putih,
    // dari batas atas jelas perlu warna, dan di antaranya mungkin cukup hitam-putih (misalnya judul atau logo berwarna).
    'color_check' => [
        'bw_up_to' => 0.02,
        'color_from' => 0.10,
    ],

    'uploads' => [
        // Uploaded files are deleted automatically after this many hours.
        'ttl_hours' => 24,

        // Largest PDF a user may attach, and the most pages the assistant will count.
        'max_megabytes' => 25,
        'max_pages' => 1000,
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

        // Alamat yang menerima email "usulan tempat baru", dipisah koma. Kosong = pakai ADMIN_EMAIL.
        'notify_emails' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) (env('ADMIN_NOTIFY_EMAILS') ?: env('ADMIN_EMAIL'))),
        ))),
    ],

];
