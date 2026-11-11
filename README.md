# CariPrint

Website untuk mahasiswa di sekitar CitraLand Surabaya yang ingin mencari tempat printing: melihat mana yang sedang buka, membandingkan harga, dan mengetahui jaraknya dari lokasi kamu. Website ini hanya menghubungkan mahasiswa dengan tempat printing. Tidak ada pemesanan dan pembayaran di dalamnya.

**Website:** https://cariprint.lius.my.id

## Tim dan pembagian kerja

Angka di bawah dihitung dari commit asli di branch `main` (tanpa merge), per 11 November 2026.

| Anggota | Area utama | Commit |
|---|---|---|
| Bryan Carlie Lukito Setiawan | Fondasi project, desain, asisten AI, impor data survei, deploy | 38 |
| Hanzelius (`Infinite-cpu-ai`) | Direktori tempat (daftar, peta, detail, halaman perbandingan) | 36 |
| Mario Ruby Ariesusandi | Panel admin, validasi data, pembaca foto harga, perapian kode | 31 |

## Status fitur

Status: ✅ selesai · 🚧 sebagian · ⏳ belum dikerjakan

| Fitur | Status | Keterangan |
|---|---|---|
| Daftar tempat printing dengan status buka (Buka, Segera tutup, Tutup) | ✅ | Bisa diurutkan dari yang terdekat atau termurah, dan difilter yang masih buka |
| Peta yang tersinkron dengan daftar | ✅ | Klik titik untuk membuka halaman tempatnya |
| Halaman detail tempat | ✅ | Jam buka, layanan, daftar harga, tombol WhatsApp |
| Halaman perbandingan harga | ✅ | Harga layanan yang sama dari semua tempat, dengan yang termurah ditandai |
| Panel admin (`/admin`) | ✅ | Kelola data tempat dan harga, hanya untuk admin |
| Impor data survei | 🚧 | Impor sudah jalan. Data tempat dari Google Maps sudah masuk (9 tempat), harga belum |
| Asisten cetak (AI) | 🚧 | Kalimat kebutuhan cetak diubah jadi spesifikasi, lalu dihitung estimasi per tempat. Estimasi di server menunggu kunci Gemini |
| Jarak dan waktu tempuh lewat Routes API | ⏳ | Saat ini jarak garis lurus |
| Rekomendasi dan template pesan WhatsApp dari asisten | ⏳ | |
| Login Google dan batas pemakaian AI | ⏳ | |
| Laporan data salah dari pengguna | ⏳ | |
| Unggah dan cek file PDF (halaman berwarna) | ⏳ | |
| Pindah database ke Supabase | ⏳ | Saat ini masih SQLite |

## Data

- **Lokasi, alamat, nomor telepon, dan jam buka** diambil dari Google Maps dengan alat Apify.
- **Harga belum tersedia** dari sumber mana pun. Harga akan ditanyakan langsung ke pemilik tempat dan hanya ditampilkan setelah dikonfirmasi. Harga yang sudah dikonfirmasi diberi tanda *Terverifikasi*, sedangkan harga yang belum disurvei diberi tanda *Perkiraan*.
- Lokasi pengguna hanya dipakai untuk menghitung jarak dan tidak disimpan.

## Teknologi

Laravel, Inertia, React, Tailwind CSS, Filament (panel admin), Gemini (asisten AI), Leaflet dengan OpenStreetMap (peta), SQLite. Tes otomatis dan pengecekan kode berjalan di GitHub Actions setiap ada perubahan di `main`. Setelah tes lulus, perubahan dikirim otomatis ke server.

## Menjalankan di komputer lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

Lalu buka http://127.0.0.1:8000. Untuk asisten AI, isi `GEMINI_API_KEY` di `.env`.

## Dokumen

- [docs/ROADMAP.md](docs/ROADMAP.md): daftar task lengkap dan pembagiannya.
- [docs/SURVEY.md](docs/SURVEY.md): format data survei.
- [docs/DEPLOY.md](docs/DEPLOY.md): cara deploy ke server.
- [docs/DEMO.md](docs/DEMO.md): naskah demo.
