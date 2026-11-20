# CariPrint — Catatan Serah Terima

Dokumen ini untuk anggota tim (dan Claude Code mereka) yang mulai bekerja dari nol. Aturan teknis ada di `CLAUDE.md` dan `.ai/`; dokumen ini menjelaskan **status**, **keputusan yang sudah diambil**, dan **sisa pekerjaan**. Baca keduanya sebelum mengubah kode.

Terakhir diperbarui: 20 November 2026. Tidak ada rahasia di sini; semua nilai `.env` diminta ke anggota tim yang memegangnya.

## 1. Produk singkat

Situs untuk mahasiswa sekitar CitraLand Surabaya membandingkan tempat print: lokasi, jam buka, status buka, layanan, daftar harga, kontak WhatsApp. CariPrint hanya mempertemukan mahasiswa dan tempat print; tidak ada pesanan dan tidak ada pembayaran.

- Repo: https://github.com/Jeruk-Kurej/CariPrint (pemilik akun: Jeruk-Kurej)
- Situs live: https://cariprint.lius.my.id (server UC, CloudPanel, deploy otomatis lewat GitHub Actions)
- Tim: Bryan, Hanzelius, Mario
- Sumber cakupan: `docs/proposal.pdf`. Desain: `docs/DESIGN.md`. Deploy: `docs/DEPLOY.md`. Survei data: `docs/SURVEY.md` (sebagian usang, lihat bagian 6).
- Bahasa: tampilan Indonesia; kode, nama, dan commit Inggris.

## 2. Aturan kerja yang disepakati (selain `CLAUDE.md`)

1. **Jangan pernah ada nama Claude/AI di GitHub.** Tidak ada `Co-Authored-By`, "Generated with", atau tautan sesi di commit, PR, atau kode.
2. Commit memakai identitas orang yang bekerja (`git config user.name` / `user.email`). Periksa dulu sebelum commit; di satu laptop tim identitas bawaannya masih milik anggota lain.
3. Agen mengusulkan pesan commit dan menunggu persetujuan. Jangan push, merge, atau menulis ulang riwayat tanpa perintah eksplisit.
4. Jangan memakai layanan berbayar atau yang memerlukan kartu (Google Routes/Places sudah dibuang). Data lokasi dari survei tim dan Apify.
5. Jangan menampilkan data karangan sebagai data nyata. Harga yang belum dari survei harus berlabel **Perkiraan**.
6. Jangan menyimpan atau mencatat lokasi pengguna.
7. Tampilan harus tetap konsisten dengan tema "papan pengumuman kampus" (token di `resources/css/app.css`). Jangan menambah warna hex acak.
8. Jangan menambah paket di luar daftar di `CLAUDE.md` tanpa bertanya.
9. Satu tugas satu cabang; merge dengan `--no-ff` setelah semua cek lolos (`composer ci:check`).

## 3. Yang sudah selesai dan live

- **Direktori**: daftar + peta (Leaflet, tile Esri), urut jarak, filter, halaman detail, badge Terverifikasi / Perkiraan, status buka.
- **Asisten AI** (Gemini 2.5 Flash, output terstruktur): permintaan teks → spesifikasi cetak → estimasi biaya per tempat. Kartu hasil menampilkan jarak dan jam buka. Pesan WhatsApp siap kirim belum ada (lihat bagian 5). AI tidak pernah membuat harga/jarak/status buka; itu dihitung PHP (`app/Services/`).
- **Cek berkas & optimasi warna** (pdf.js di browser).
- **Bandingkan harga** dengan filter, tata letak tidak melebar ke kanan. Estimasi per tempat menandai semua yang termurah.
- **Login Google** (Socialite) untuk pengguna; admin masuk dengan email + kata sandi. Tidak ada registrasi publik admin (`php artisan cariprint:create-admin`).
- **Usul tempat baru** (wajib login): menyimpan `ShopSuggestion`, email ke semua admin (`ADMIN_NOTIFY_EMAILS`), tanda terima ke pengusul, email keputusan saat diterima/ditolak. Email bertema sama dengan situs.
- **Halaman Akunku**, menu akun, halaman **Privasi** (`/privasi`).
- **Panel admin Filament** di `/admin` dengan tema serupa situs, dasbor (ringkasan tempat, usulan menunggu), avatar inisial lokal, tautan kembali ke situs.
- **Kuota AI**: tamu 3/hari (per sesi browser, plafon IP 60/hari), pengguna 15/hari, admin tanpa batas, reset tengah malam WIB (`app/Services/Ai/AiQuota.php`). Sisa kuota tampil di halaman Asisten. Kuota hanya berkurang kalau AI menjawab.
- Footer disederhanakan (hanya tautan Privasi). Panel admin memberi peringatan sebelum meninggalkan formulir yang belum disimpan.
- Logo/favicon baru. CI: Pest (219 tes), PHPStan level 7, Pint, lint, tsc. Deploy otomatis setelah tes hijau di `main`.
- Dependabot sudah dimatikan (config dihapus).

## 4. Belum terbukti di produksi (perlu dicek manusia)

- Login Google untuk akun di luar tim sudah berhasil diuji di situs live (akun kampus baru langsung dibuatkan akun dan masuk ke `/akun`). Aplikasi OAuth sudah berstatus _In production_ sejak 18 November 2026.
- Email SMTP sudah terbukti jalan (tanda terima dan email admin diterima di produksi). Yang belum dicek manusia: email keputusan setelah admin menerima/menolak, dan status usulan berubah di `/akun`.
- Pengujian kuota AI di server nyata (tamu kehabisan jatah, login menaikkan batas).
- Tampilan email dan tema admin di perangkat asli; favicon baru mungkin tertahan cache.

## 5. Pekerjaan tersisa (urut saran)

1. **Google OAuth: selesai.** Aplikasi sudah di-publish (In production), tanpa logo dan hanya scope dasar sehingga tidak butuh verifikasi. Jangan mengunggah logo di layar Branding Google Cloud Console, karena itu memicu kewajiban verifikasi. Redirect URI terdaftar: `https://cariprint.lius.my.id/masuk/google/callback` dan `http://127.0.0.1:8000/masuk/google/callback`.
2. **Revisi dosen 1 (deteksi area baru) — belum dikerjakan.** Desain yang disepakati: aplikasi hanya mencatat area (sel kasar) yang belum punya tempat, **tidak pernah koordinat pengguna**. Setelah area punya tempat tersimpan, pengunjung berikutnya memakai data itu. Pengambilan data dipicu **admin** (kredit Apify terbatas, tanpa layanan berbayar), hasil masuk sebagai **draf** sampai admin memeriksa. Butuh status Draf/Terbit pada tempat dan semua query publik menyembunyikan draf. Belum dikonfirmasi apakah akun Apify dibuat tanpa kartu kredit.
3. **Harga asli.** Harga sekarang berasal dari `EstimatedPriceSeeder` (angka karangan, berlabel Perkiraan). Kontak WhatsApp tempat print ada di `kontak-whatsapp.csv`; template pesan di `config/cariprint.php` (`survey.whatsapp_message`). Isi harga yang dibalas pemilik sebagai Terverifikasi. Foto daftar harga bisa dibaca dengan `php artisan survey:read-prices` lalu diperiksa manusia. Catatan: daftar harga Kozko hanya format besar, tidak cocok skema (A4/F4/A3).
4. **Pesan WhatsApp siap kirim dari asisten** belum ada. Asisten baru menghitung estimasi per tempat.
5. **Dokumen**: `docs/SURVEY.md` usang (baris `place_id`, tidak menyebut `survey:convert-apify`, `survey:read-prices`, `database/data`); panduan presentasi belum memuat fitur baru. Tabel progres `README.md` sudah diperbarui 20 November (README ini dikumpulkan ke dosen, jaga tetap jujur).
6. **Sisa desain**: penanda Perkiraan/Terverifikasi per sel di tabel Bandingkan (butuh data backend), header tabel lengket, stabilo kuning masih dipakai dekoratif di hero (seharusnya hanya untuk harga).
7. Kegagalan email hanya tercatat di `storage/logs/laravel.log` (pengguna tidak melihat apa-apa). Pertimbangkan menampilkannya di dasbor admin.

## 6. Keputusan dan jebakan yang sudah dipelajari

- Sumber harga hanya `verified` dan `estimated`. Sumber `demo` sudah dihapus.
- Uang disimpan integer Rupiah. Dari Google hanya `place_id` yang boleh disimpan.
- Peta memakai tile Esri; CARTO meminta API key dan OSM memblokir akses dari server.
- Wayfinder: setelah mengubah rute jalankan `php artisan wayfinder:generate --with-form` (tanpa `--with-form`, helper `.form` hilang) atau `npm run build`.
- Di CI, `.env.example` disalin sehingga nilai kosong menjadi `''`, bukan `null`. Pakai `env('X') ?: null` di config. Uji dengan `CONTACT_EMAIL= php artisan test`.
- Server harus memiliki `GOOGLE_REDIRECT_URI` eksplisit. Setelah mengubah `.env` di server: `php artisan config:clear && php artisan optimize`.
- Callback Google yang gagal sempat tampak "diam"; sekarang galat dicatat (`report()`) dan pesan dibedakan. Cek `storage/logs` bila login gagal.
- Login Google mengklaim akun yang sudah ada berdasarkan email terverifikasi; nama generik ("Admin") diganti nama Google.
- Tidak ada worker antrean permanen; jadwal lewat cron `schedule:run`. Email dikirim sinkron.
- Gmail SMTP memakai _App Password_ 16 huruf, **tanpa spasi** di `.env` (spasi merusak parsing dotenv dan seluruh situs). Kata sandi akun biasa ditolak (galat 535). Jangan menempelkan `.env` atau layar yang memuat kata sandi ke obrolan.
- Nginx CloudPanel menjawab 404 untuk `.js` yang bukan berkas nyata, sehingga `livewire.min.js` tidak termuat dan panel admin kosong. Skrip deploy menjalankan `php artisan filament:assets` dan `php artisan livewire:publish --assets` untuk mengatasinya.
- Kuota tamu dihitung per cookie sesi, jadi saat demo dari satu browser sebagai tamu hanya 3 kali. Masuk atau pakai akun admin.
- Roadmap pribadi `docs/ROADMAP.md` sengaja tidak masuk repo (gitignore). Satu-satunya laporan progres untuk dosen adalah tabel di `README.md`.

## 7. Menyiapkan lingkungan (anggota baru)

1. Undang ke repo (Settings → Collaborators) dan pastikan `gh auth login` / git memakai akun sendiri.
2. `git clone`, lalu ikuti `docs/SETUP.md` (composer, npm, `.env`, `php artisan migrate --seed`).
3. Minta nilai `.env` ke Bryan lewat jalur pribadi (jangan lewat repo atau grup publik): `GEMINI_API_KEY`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `MAIL_*`, `ADMIN_EMAIL`, `ADMIN_NOTIFY_EMAILS`, `CONTACT_EMAIL`. Untuk lokal, `MAIL_MAILER=log` cukup.
4. Buat akun admin lokal: `php artisan cariprint:create-admin`.
5. Cek: `composer ci:check` harus hijau sebelum merge.
6. Akses server (SSH/CloudPanel) dan secret GitHub Actions dipegang pemilik; lihat `docs/DEPLOY.md`.
7. Untuk Google OAuth di lokal, akun Google apa pun bisa dipakai selama redirect URI lokal (`http://127.0.0.1:8000/masuk/google/callback`) terdaftar; tidak perlu lagi menjadi penguji.

## 8. Cara memulai sesi Claude Code

Buka folder proyek, lalu tempel:

> Baca `CLAUDE.md`, `.ai/`, dan `docs/HANDOFF.md` dulu. Ringkas status proyek dengan kata-katamu sendiri, lalu tanyakan padaku tugas mana yang kukerjakan. Untuk tugas non-trivial, ajukan rencana dulu dan tunggu persetujuan. Jangan commit/push tanpa kuminta, dan jangan menambahkan atribusi Claude di mana pun.

Bagian tersulit untuk agen baru: (a) revisi 1 butuh keputusan produk, jadi minta agen merencanakan dulu; (b) jangan menyuruh agen "mengisi harga" tanpa sumber, karena harga karangan tidak boleh tampil sebagai data nyata.
