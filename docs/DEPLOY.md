# Deploy CariPrint

Website dijalankan di server CloudPanel (Nginx dan PHP-FPM) di `https://cariprint.lius.my.id`. Dokumen ini menjelaskan cara kerja deploy sekarang dan langkah yang hanya perlu dikerjakan sekali.

## Cara kerja sekarang

1. Setiap push atau pull request menjalankan **tes otomatis** (workflow `tests`): format, lint, analisis kode statis (PHPStan), type check, dan tes Pest.
2. Setelah tes di `main` lulus, workflow **deploy** berjalan otomatis. GitHub membangun aplikasi, lalu mengirimnya ke server lewat SSH dan menjalankan update.
3. Yang dijalankan di server: `git reset` ke `main`, `composer install --no-dev`, `php artisan migrate --force`, dan optimasi cache. Tidak perlu SSH untuk update biasa.

Akibatnya, **setiap merge ke `main` yang lulus tes langsung tayang**. Pastikan tim tahu hal ini sebelum merge.

Kalau deploy otomatis gagal, atau perlu update manual, jalankan di server:

```bash
cd ~/htdocs/cariprint.lius.my.id
bash scripts/deploy.sh
```

## Setup yang hanya dikerjakan sekali

### 1. Domain dan SSL

- Arahkan A record `cariprint` ke IP publik server (`103.103.138.174`), bukan IP internal yang tampil di CloudPanel.
- Di CloudPanel, buat sertifikat Let's Encrypt untuk site.
- Root directory site: `cariprint.lius.my.id/public`.

### 2. Akses SSH ke server

Site User di CloudPanel (`lius-cariprint`) dipakai untuk login SSH. Ada dua cara:

- **Kata sandi**: kata sandi Site User. Cukup untuk tugas ini, tapi kurang aman.
- **Kunci SSH** (lebih disarankan): buat kunci di komputer, tempel kunci publiknya ke CloudPanel (Site User Settings → SSH Keys), dan simpan kunci privatnya sebagai secret di GitHub.

Jangan pernah menulis kata sandi atau kunci privat di repo atau di chat.

### 3. Secret dan variable di GitHub

Settings → Secrets and variables → Actions:

| Jenis    | Nama              | Isi                                                                                    |
| -------- | ----------------- | -------------------------------------------------------------------------------------- |
| Secret   | `SSH_HOST`        | `cariprint.lius.my.id`                                                                 |
| Secret   | `SSH_USERNAME`    | `lius-cariprint`                                                                       |
| Secret   | `SSH_PASSWORD`    | kata sandi Site User (salah satu dari `SSH_PASSWORD` atau `SSH_PRIVATE_KEY` wajib ada) |
| Secret   | `SSH_PRIVATE_KEY` | isi kunci privat SSH (alternatif `SSH_PASSWORD`)                                       |
| Variable | `APP_PATH`        | `/home/lius-cariprint/htdocs/cariprint.lius.my.id`                                     |
| Variable | `SSH_PORT`        | opsional, default `22`                                                                 |

Kalau secret belum lengkap, workflow deploy berhenti di langkah "Check deploy configuration" dengan pesan yang menyebut secret mana yang kurang. Itu tidak mengubah server.

### 4. File `.env` di server

`.env` tidak masuk git dan tidak diubah oleh deploy. Isi sekali di server:

```env
APP_NAME=CariPrint
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cariprint.lius.my.id
APP_LOCALE=id
DB_CONNECTION=pgsql
DB_HOST=<host Session pooler Supabase>
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=<username pooler>
DB_PASSWORD=<kata sandi database>
DB_SSLMODE=require
LOG_LEVEL=error
ADMIN_EMAIL=<email admin>
ADMIN_NOTIFY_EMAILS=<email penerima notifikasi usulan, dipisah koma>
GEMINI_API_KEY=<kunci Gemini, opsional>

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.purelymail.com
MAIL_PORT=465
MAIL_USERNAME=<alamat user Purelymail, mis. noreply@lius.my.id>
MAIL_PASSWORD=<kata sandi atau app password user Purelymail>
MAIL_FROM_ADDRESS=<alamat yang sama dengan MAIL_USERNAME>
MAIL_FROM_NAME=CariPrint

GOOGLE_CLIENT_ID=<dari Google Cloud Console>
GOOGLE_CLIENT_SECRET=<dari Google Cloud Console>
GOOGLE_REDIRECT_URI=https://cariprint.lius.my.id/masuk/google/callback
```

Akun admin dibuat dengan `php artisan cariprint:create-admin <email>` (kata sandi diketik tersembunyi), bukan lewat `ADMIN_PASSWORD`. Di Google Cloud Console, tambahkan `https://cariprint.lius.my.id/masuk/google/callback` sebagai Authorized redirect URI.

Email dikirim lewat Purelymail milik kelas. Domain pengirim harus sudah terdaftar dan terverifikasi di Purelymail (record MX, SPF, DKIM, dan DMARC dari halaman Domains dipasang di DNS), dan alamat pengirim dibuat sebagai User di Purelymail.

Setelah mengubah `.env`, jalankan `php artisan optimize`.

Database production ada di Supabase. Server butuh driver `pdo_pgsql` (paket `php8.4-pgsql` untuk CLI dan `php8.5-pgsql` untuk PHP-FPM), yang dipasang oleh pengelola server.

### 5. Penyiapan pertama di server

```bash
cd ~/htdocs/cariprint.lius.my.id
rm -rf public                       # hapus halaman bawaan CloudPanel
git clone https://github.com/Jeruk-Kurej/CariPrint.git .
composer install --no-dev --optimize-autoloader
cp .env.example .env && nano .env   # isi seperti langkah 4
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
npm ci && npm run build
php artisan optimize
```

### 6. Data survei

Setelah ada berkas survei di `database/data/survey-shops.csv` dan `survey-prices.csv` (lihat `SURVEY.md`):

```bash
php artisan db:seed --class=SurveyShopSeeder --force
```

Kalau database server masih berisi toko contoh dari seeder awal, hapus dengan:

```bash
php artisan tinker --execute 'App\Models\Shop::where("slug", "like", "contoh-%")->delete();'
```

## Cron

Belum diperlukan. Scheduler (`schedule:run`) baru dibutuhkan saat ada tugas terjadwal, misalnya menghapus unggahan PDF setelah 24 jam (Phase 6 di roadmap). Saat itu, tambahkan cron di CloudPanel:

```
* * * * * php /home/lius-cariprint/htdocs/cariprint.lius.my.id/artisan schedule:run >> /dev/null 2>&1
```

## Kalau ada masalah

- **Deploy merah di tab Actions**: buka log langkah yang gagal.
    - "belum diisi": secret atau variable di GitHub belum lengkap.
    - "npm ci" gagal: `package-lock.json` tidak sinkron dengan `package.json`. Jalankan `npm install --package-lock-only` di komputer, lalu commit.
    - Gagal di migrate: cek `storage/logs/laravel.log` di server.
- **Situs error setelah deploy**: di server, `tail -50 storage/logs/laravel.log`. Jangan pernah mengaktifkan `APP_DEBUG=true` di production.
- **Mengubah `.env`**: jalankan `php artisan optimize:clear && php artisan optimize`.
