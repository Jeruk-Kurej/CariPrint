# Setup Lokal

Panduan untuk semua anggota tim.

## 1. Prasyarat

- PHP sesuai `composer.json`, Composer, Git
- Node.js 22 (Vite butuh minimal 20.19)
- Akun Supabase sendiri (gratis), baru dibutuhkan saat ROADMAP Phase 2

## 2. Clone dan install

```bash
git clone <url-repo> cariprint
cd cariprint
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## 3. Database development

### Sekarang: SQLite

Sampai ROADMAP Phase 2 (pindah ke Supabase), project memakai SQLite bawaan starter kit. Tidak perlu setup apa pun:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

Kalau ada migration baru setelah `git pull`, jalankan `php artisan migrate`. Kalau database lokal berantakan, reset dengan `php artisan migrate:fresh --seed`.

### Nanti: Supabase (setelah materi dari tutor)

Setiap anggota memakai **project Supabase sendiri** untuk development. Database production hanya satu (dipegang Bryan) dan tidak dipakai untuk development.

Alasannya: kalau bertiga memakai satu database, migration dari branch yang berbeda akan saling bentrok, dan data production bisa ikut rusak.

1. Buat project baru di Supabase, region **Southeast Asia (Singapore)** (paling dekat ke Surabaya).
2. Klik **Connect** → pilih **Session pooler** → copy connection string-nya.
3. Isi di `.env`:

```env
DB_CONNECTION=pgsql
DB_URL="postgresql://postgres.<project-ref>:<password>@<host-pooler>:5432/postgres"
```

Pakai **Session pooler**, bukan Direct connection atau Transaction pooler:
- Direct connection Supabase hanya lewat IPv6, sedangkan banyak jaringan (termasuk server kampus) hanya IPv4.
- Transaction pooler (port 6543) tidak cocok dengan prepared statements yang dipakai Laravel.

4. Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

## 4. Menjalankan project

```bash
composer run dev
```

Buka http://localhost:8000.

## 5. Alur kerja harian

```bash
# Mulai task baru
git switch main
git pull
git switch -c feat/<area>-<deskripsi>

# Selesai task: update dari main, cek, lalu merge
git merge main
vendor/bin/pint --dirty && npm run check && npm run types:check && php artisan test --compact
git switch main
git merge --no-ff feat/<area>-<deskripsi>
git push
git branch -d feat/<area>-<deskripsi>
```

Setelah `git pull` yang membawa migration baru, jalankan `php artisan migrate`.

## 6. Claude Code (opsional)

- Aturan project ada di `.ai/guidelines/cariprint.md`. `CLAUDE.md` tidak ikut di-commit, jadi setelah clone (dan setiap kali guidelines berubah) jalankan `touch CLAUDE.md && php artisan boost:update` untuk membuatnya. `touch` diperlukan karena tanpa file `CLAUDE.md`, Boost menulis aturannya ke `AGENTS.md`.
- `.claude/settings.json` sudah mematikan baris atribusi AI di commit dan PR.
- Setting pribadi taruh di `.claude/settings.local.json` (tidak ikut di-commit).
- Template prompt ada di `docs/PROMPTS.md`.
