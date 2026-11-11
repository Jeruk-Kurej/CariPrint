# Naskah demo Milestone 1 (± 6 menit)

Tujuan: menunjukkan bahwa website sudah hidup, datanya nyata, dan admin bisa mengubah data yang langsung terlihat publik.

## Persiapan (sebelum presentasi)

1. Website sudah live di `https://cariprint.lius.my.id` dan bisa dibuka dari HP.
2. Data survei sudah masuk (3–5 tempat, `php artisan db:seed` atau importer survei).
3. Kunci Google Maps sudah dipasang dan dibatasi ke domain `cariprint.lius.my.id`. Kalau belum, panel peta akan menampilkan "Peta belum aktif".
4. Akun admin sudah dibuat. Login di `/admin` sudah dicoba sebelum presentasi.
5. Buka dua tab: website di HP (atau mode 360px di laptop) dan `/admin` di laptop.

## Alur demo

| Menit     | Yang ditampilkan                                                         | Siapa     | Yang dikatakan                                                                                                       |
| --------- | ------------------------------------------------------------------------ | --------- | -------------------------------------------------------------------------------------------------------------------- |
| 0:00–0:45 | Home di HP                                                               | Hanzelius | "Ini daftar tempat printing di CitraLand, diurutkan dari yang terdekat. Statusnya sudah dihitung dari jam buka."     |
| 0:45–1:30 | Tekan "Detail" pada satu tempat                                          | Hanzelius | "Di detail ada jam buka, layanan, dan harga. Badge hijau berarti harga dari survei kami, abu-abu berarti perkiraan." |
| 1:30–2:15 | Peta dan "Lihat di peta"                                                 | Hanzelius | "Peta dan daftar saling terhubung. Klik salah satu, yang lain ikut menyorot."                                        |
| 2:15–3:45 | Login admin, ubah satu harga                                             | Mario     | "Admin mengubah harga di sini. Tanggal diubah otomatis ikut berubah."                                                |
| 3:45–4:15 | Refresh halaman detail di HP                                             | Mario     | "Perubahan tadi langsung terlihat di halaman publik, tanpa deploy ulang."                                            |
| 4:15–5:00 | Arsitektur singkat: Laravel, Inertia React, SQLite, CloudPanel, alur Git | Bryan     | "Logika harga dan status buka dihitung di PHP dan dites. AI tidak menghitung harga."                                 |
| 5:00–5:30 | Asisten cetak (prototipe)                                                | Bryan     | "Ini prototipe: kalimat kebutuhan cetak diubah jadi spesifikasi, lalu dihitung dari daftar harga."                   |
| 5:30–6:00 | Rencana berikutnya                                                       | Semua     | "Berikutnya: Supabase, login Google, Smart File Check, dan laporan dari user."                                       |

## Kalau ada yang gagal saat demo

- **Peta tidak muncul:** tunjukkan daftar, jelaskan bahwa peta menunggu kunci API.
- **Asisten cetak tidak merespons:** tunjukkan kodenya dan hasil tes, jelaskan bahwa butuh kunci Gemini.
- **Website mati:** tampilkan screenshot dan video singkat yang sudah disiapkan.

## Pertanyaan yang kemungkinan muncul

- _Kenapa SQLite dulu?_ Supaya tiap anggota bisa jalan tanpa install apa-apa. Migrasi ke Supabase tinggal ganti koneksi di `.env`.
- _Data harga dari mana?_ Dari survei langsung tim ke tiap tempat, dicatat dengan format yang sama (`docs/SURVEY.md`).
- _Kenapa AI tidak menghitung harga?_ Supaya angka selalu bisa dicek dan dites. AI hanya membaca kalimat user.
