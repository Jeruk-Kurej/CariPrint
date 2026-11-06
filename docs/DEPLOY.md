# Deploy ke CloudPanel

Server: CloudPanel UC, site `cariprint.lius.my.id`, site user `lius-cariprint`, IP publik `103.103.138.174` (CloudPanel menampilkan IP internal `103.103.138.163`).
Yang deploy hanya **satu orang (Bryan)**, supaya tidak ada dua deploy yang jalan bersamaan.

## A. Setup pertama kali

### 1. DNS dan SSL

1. DomaiNesia: A record `cariprint` → `103.103.138.174` (IP publik dari tutor, bukan IP yang tampil di CloudPanel).
2. Cek dari Mac: `dig cariprint.lius.my.id +short`. Hasilnya harus `103.103.138.174`.
3. CloudPanel → site → **SSL/TLS** → buat sertifikat Let's Encrypt.

### 2. CloudPanel

- **Settings** → Root Directory: `cariprint.lius.my.id/public` (sudah benar).
- **Settings** → PHP version minimal sesuai `composer.json`.

### 3. SSH

```bash
ssh lius-cariprint@cariprint.lius.my.id
```

Password dari Site User Settings. Supaya tidak perlu password, tempel isi `~/.ssh/id_ed25519.pub` dari Mac ke kolom **SSH Keys** di Site User Settings.

### 4. Akses repo dari server (deploy key, read-only)

Jangan pernah menaruh personal access token di URL remote.

```bash
ssh-keygen -t ed25519 -C "cariprint-server" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

Copy hasilnya ke GitHub → repo → **Settings → Deploy keys → Add deploy key** (jangan centang write access). Lalu tes dengan `ssh -T git@github.com`.

### 5. Node 22

Node bawaan server versi 18, sedangkan Vite butuh minimal 20.19. Install Node 22 di akun site user:

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
source ~/.bashrc
nvm install 22
node -v
```

Kalau gagal, alternatifnya build di Mac lalu upload hasilnya:
`npm run build && scp -r public/build lius-cariprint@cariprint.lius.my.id:~/htdocs/cariprint.lius.my.id/public/`

### 6. Clone dan install

Folder site harus kosong dulu (cek dengan `ls -A`).

```bash
cd ~/htdocs/cariprint.lius.my.id
git clone git@github.com:<owner>/<repo>.git .
composer install --no-dev --optimize-autoloader
cp .env.example .env
nano .env
```

Isi `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cariprint.lius.my.id
DB_CONNECTION=sqlite
```

Setelah pindah ke Supabase (ROADMAP Phase 2), ganti jadi:

```env
DB_CONNECTION=pgsql
DB_URL="<session pooler Supabase production>"
```

Lalu:

```bash
touch database/database.sqlite
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
npm ci && npm run build
php artisan optimize
```

### 7. Cron

CloudPanel → **Cron Jobs** → tambah, jadwal `* * * * *`:

```
php /home/lius-cariprint/htdocs/cariprint.lius.my.id/artisan schedule:run >> /dev/null 2>&1
```

Kalau `php` mengarah ke versi yang salah, pakai path lengkap sesuai versi site (misalnya `/usr/bin/php8.4`). Scheduler ini yang menjalankan hapus file 24 jam dan memproses antrean.

## B. Update rutin

Setelah ada perubahan di `main`:

```bash
cd ~/htdocs/cariprint.lius.my.id
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize:clear && php artisan optimize
```

Perintah ini nanti dijadikan `scripts/deploy.sh` (ROADMAP task 0.5).

## C. Kalau error

- `tail -50 storage/logs/laravel.log`
- Jangan mengaktifkan `APP_DEBUG=true` di production, karena stack trace dan path server bisa terlihat publik.
- Setelah mengubah `.env`, jalankan `php artisan optimize:clear && php artisan optimize`.
