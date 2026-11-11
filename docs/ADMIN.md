# Panduan admin CariPrint

Masuk di `/admin` memakai email dan password admin. Akun admin dibuat lewat seeder atau perintah `php artisan cariprint:create-admin`. Tidak ada pendaftaran publik.

## Menambah tempat printing

1. Menu **Tempat printing** → **Buat**.
2. Isi nama, alamat, dan nomor WhatsApp (format `62...`, tanpa `+` atau spasi).
3. Isi koordinat dari Google Maps. Koordinat harus di dalam area CitraLand, kalau tidak form menolak.
4. Isi jam buka per hari. Tambah satu baris per sesi buka. Hari libur dikosongkan.
5. Centang layanan yang tersedia, lalu isi aturan halaman warna dari penjelasan penjaga.

## Mengubah harga

1. Buka tempat printing, lalu tab **Harga**.
2. Tambah atau ubah baris. Harga diisi dalam Rupiah bulat, tanpa titik atau koma.
3. Pilih **Sumber harga**: _Terverifikasi (survei)_ untuk data dari survei tim, _Perkiraan_ untuk data yang belum disurvei.
4. Tanggal "diubah" tempat itu otomatis ikut diperbarui, dan pengunjung melihatnya di halaman detail.

## Arti badge

- **Buka**, **Segera tutup**, **Tutup**: dihitung otomatis dari jam buka. "Segera tutup" muncul 30 menit sebelum tutup.
- **Terverifikasi**: harga dari survei tim.
- **Perkiraan**: harga belum disurvei, bisa berbeda dengan harga sebenarnya.

## Kalau ada data yang salah

Perbaiki langsung lewat admin. Data yang salah juga bisa dilaporkan pengguna lewat fitur laporan (task 5.4, setelah Milestone 1).
