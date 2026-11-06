# CariPrint

Website pencarian tempat printing di area CitraLand Surabaya. Mahasiswa bisa membandingkan lokasi, jam operasional, layanan, dan harga tanpa menghubungi tempat printing satu per satu. CariPrint hanya menjadi penghubung: tidak ada pemesanan maupun pembayaran di website.

**Live:** https://cariprint.lius.my.id

## Tim

| Nama                         | NIM           | Area                                              |
| ---------------------------- | ------------- | ------------------------------------------------- |
| Bryan Carlie Lukito Setiawan | 0706012410025 | Fondasi, deploy, AI Print Agent, Smart File Check |
| Hanzelius                    | 0706012410008 | Printing Shop Directory                           |
| Mario Ruby Ariesusandi       | 0706012410028 | Admin Panel, laporan, data survei                 |

## Progress fitur

Status: ✅ selesai · 🚧 dikerjakan · ⏳ belum dimulai

| Fitur                                    | Status | Keterangan                                       |
| ---------------------------------------- | ------ | ------------------------------------------------ |
| Fondasi project (schema, layout, deploy) | ⏳     |                                                  |
| Printing Shop Directory                  | ⏳     | List, peta, detail shop, status buka, urut jarak |
| Admin Panel                              | ⏳     | Kelola data tempat printing dan harga            |
| Data survei tempat printing              | ⏳     | 0 tempat tersurvei                               |
| AI Print Agent                           | ⏳     |                                                  |
| Smart File Check & Color Optimization    | ⏳     |                                                  |
| Login Google dan batas penggunaan AI     | ⏳     |                                                  |
| Laporan dari user                        | ⏳     |                                                  |
| Migrasi database ke Supabase             | ⏳     | Menunggu materi Supabase                         |

Rincian task ada di [docs/ROADMAP.md](docs/ROADMAP.md).

## Tech stack

- **Backend:** Laravel, Pest
- **Frontend:** Inertia, React, TypeScript, Tailwind CSS, shadcn/ui
- **Database:** SQLite (sementara), lalu Supabase (PostgreSQL, Storage)
- **AI:** Gemini Flash melalui Laravel AI SDK
- **Admin panel:** Filament
- **Maps:** Google Maps JavaScript API, Places API, Routes API
- **Deployment:** server UC dengan CloudPanel, domain DomaiNesia, GitHub

## Dokumentasi

- [Setup lokal](docs/SETUP.md)
- [Deploy](docs/DEPLOY.md)
- [Proposal](docs/proposal.pdf)
