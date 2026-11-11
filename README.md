# CariPrint

Website pencarian tempat printing di area CitraLand Surabaya. Mahasiswa bisa membandingkan lokasi, jam buka, layanan, dan harga tanpa menghubungi tempat printing satu per satu. CariPrint hanya menjadi penghubung: tidak ada pemesanan maupun pembayaran di website.

**Live:** https://cariprint.lius.my.id _(segera, setelah deploy Milestone 1)_

## Tim

| Nama                         | NIM           | Area                                              |
| ---------------------------- | ------------- | ------------------------------------------------- |
| Bryan Carlie Lukito Setiawan | 0706012410025 | Fondasi, deploy, AI Print Agent, Smart File Check |
| Hanzelius                    | 0706012410008 | Printing Shop Directory                           |
| Mario Ruby Ariesusandi       | 0706012410028 | Admin Panel, laporan, data survei                 |

## Yang sudah jalan (Milestone 1)

- **Daftar tempat printing** urut jarak dari lokasi kamu, dengan status buka (Buka / Segera tutup / Tutup).
- **Peta** yang tersinkron dengan daftar.
- **Halaman detail** berisi jam buka, layanan, dan harga. Harga diberi label _Terverifikasi_ (survei tim) atau _Perkiraan_.
- **Admin** (`/admin`): kelola data tempat dan harga. Perubahan langsung terlihat di halaman publik.
- **Asisten cetak** (prototipe): kalimat kebutuhan cetak diubah jadi spesifikasi, lalu dihitung estimasi per tempat.

Rincian task ada di [docs/ROADMAP.md](docs/ROADMAP.md). Naskah demo ada di [docs/DEMO.md](docs/DEMO.md).

## Progres

Status: ✅ selesai · 🚧 dikerjakan · ⏳ belum dimulai

| Fitur                                    | Status | Keterangan                                                                |
| ---------------------------------------- | ------ | ------------------------------------------------------------------------- |
| Fondasi project (schema, layout, deploy) | 🚧     | Schema, aturan project, seeder admin, layout publik selesai. Deploy belum |
| Printing Shop Directory                  | 🚧     | Home urut jarak, peta, dan detail shop selesai. Belum live                |
| Admin Panel                              | 🚧     | Login admin, CRUD tempat dan harga selesai. Laporan belum                 |
| Data survei tempat printing              | ⏳     | Importer siap, data survei belum dimasukkan                               |
| AI Print Agent                           | 🚧     | Prototipe. Perlu API key Gemini                                           |
| Smart File Check & Color Optimization    | ⏳     |                                                                           |
| Login Google dan batas penggunaan AI     | ⏳     |                                                                           |
| Laporan dari user                        | ⏳     |                                                                           |
| Migrasi database ke Supabase             | ⏳     | Menunggu materi Supabase                                                  |

## Tech stack

- **Backend:** Laravel 13, Pest, Filament (admin)
- **Frontend:** Inertia, React, TypeScript, Tailwind CSS, shadcn/ui
- **Database:** SQLite (sementara), lalu Supabase (PostgreSQL, Storage)
- **AI:** Gemini Flash melalui Laravel AI SDK
- **Maps:** Google Maps JavaScript API
- **Deployment:** server UC dengan CloudPanel, domain DomaiNesia, GitHub

## Menjalankan di komputer kamu

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
composer run dev
```

Buka http://localhost:8000. Untuk admin, isi `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env` sebelum seeding, atau jalankan `php artisan cariprint:create-admin email@kamu.com`.

Kunci opsional di `.env`:

- Peta memakai OpenStreetMap dan tidak butuh API key.
- `GEMINI_API_KEY`: aktifkan asisten cetak.

Panduan lengkap ada di [docs/SETUP.md](docs/SETUP.md).

## Dokumentasi

- [Setup lokal](docs/SETUP.md)
- [Deploy](docs/DEPLOY.md)
- [Panduan admin](docs/ADMIN.md)
- [Format survei](docs/SURVEY.md)
- [Arah visual](docs/DESIGN.md)
- [Proposal](docs/proposal.pdf)
