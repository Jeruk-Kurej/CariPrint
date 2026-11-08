# CariPrint Roadmap

Status: `[ ]` belum, `[~]` sedang dikerjakan, `[x]` selesai. Task bertanda **[M1]** masuk target presentasi progress. Satu task = satu branch (lihat Git workflow di `.ai/guidelines/cariprint.md`).

## Milestone 1 — Presentasi progress

**Target demo:** website sudah live di `cariprint.lius.my.id`. Pengunjung bisa melihat daftar tempat printing yang diurutkan berdasarkan jarak, peta, dan halaman detail. Admin bisa menambah dan mengubah data shop dan harga, lalu perubahannya langsung muncul di halaman publik.

Kenapa ini cakupannya:

- **Frontend kelihatan:** directory adalah fitur inti dan halaman pertama yang dibuka user.
- **Backend kelihatan:** admin mengubah data → halaman publik berubah. Ini bukti ada database dan logika server, bukan halaman statis.
- **Cloud kelihatan:** website sudah live di server UC lewat CloudPanel, sesuai topik mata kuliah.
- **Nyata:** pakai data survei 3–5 tempat printing sungguhan, bukan dummy.

**Di luar Milestone 1:** Supabase (tunggu materi dari tutor), login Google, quota AI, Smart File Check, AI Agent versi lengkap, fitur laporan, waktu tempuh via Routes API.

Sementara itu database memakai **SQLite** (bawaan starter kit). Karena schema dibuat lewat migration Laravel, pindah ke Supabase nanti cukup ganti koneksi di `.env` lalu jalankan migration ulang. Syaratnya, migration tidak boleh memakai fitur khusus Postgres sampai pindah.

### Jadwal

Urutan H1–H5 = hari kerja. Geser sesuai tanggal presentasi.

| Hari | Bryan                                                               | Hanzelius                                                     | Mario                                     |
| ---- | ------------------------------------------------------------------- | ------------------------------------------------------------- | ----------------------------------------- |
| H1   | 0.1 init + push GitHub, 0.2 schema                                  | Survei 2 shop. Mulai komponen kartu shop dengan data hardcode | Survei 2 shop. Siapkan format data survei |
| H2   | 0.3 seeder, 0.4 layout publik + design tokens                       | 1.1 OpeningStatus, 1.2 home list                              | 5.1 Filament, 5.2 CRUD shop               |
| H3   | 0.5 deploy pertama                                                  | 1.3 peta                                                      | 5.3 CRUD harga                            |
| H4   | Stretch: 3.1 + 4.1 prototype AI Agent                               | 1.4 halaman detail                                            | 5.6 input data survei                     |
| H5   | Merge semua, update README progress, deploy final, gladi presentasi | Fix tampilan di HP                                            | Cek data + bantu gladi                    |

**Batas aman:** semua task [M1] (kecuali stretch) sudah masuk `main` di akhir H4, supaya H5 cuma untuk deploy dan latihan.

## Pembagian kerja

| Orang         | Area                                                                                | Alasan                                                                                                                                                                                    |
| ------------- | ----------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Bryan**     | Phase 0 (fondasi), Auth & quota, Smart File Check, AI Print Agent, migrasi Supabase | Fondasi harus dikerjakan satu orang supaya schema dan struktur konsisten. File Check dan AI Agent memakai pipeline yang sama (PDF, PriceCalculator, Gemini), jadi lebih aman satu pemilik |
| **Hanzelius** | Directory: home, peta, detail shop, status buka, jarak                              | Fitur inti dan paling sering dibuka user. Hasilnya (`OpeningStatus`) juga dipakai AI Agent                                                                                                |
| **Mario**     | Admin panel (Filament), fitur laporan, input data survei                            | Bisa jalan paralel setelah schema jadi, dan langsung terhubung dengan data hasil survei                                                                                                   |

Survei tempat printing dikerjakan bertiga.

### Kontrak bersama (jangan diubah tanpa kabar ke pemiliknya)

| Kontrak                                | Pemilik   | Dipakai oleh         |
| -------------------------------------- | --------- | -------------------- |
| Migrations dan models inti             | Bryan     | Semua                |
| `Shops/OpeningStatus`                  | Hanzelius | Directory, AI Agent  |
| `Pricing/PriceCalculator`, `PrintSpec` | Bryan     | File Check, AI Agent |

Perubahan schema setelah Phase 0 dibuat lewat migration baru di branch masing-masing, lalu kabari tim.

## Phase 0 — Fondasi (Bryan, wajib selesai dulu)

- [x] 0.1 **[M1]** Setup aturan project (bootstrap prompt): file aturan dan docs dibuat, guidelines masuk ke CLAUDE.md/AGENTS.md lewat Boost, `.gitignore` dicek, commit pertama, push ke GitHub
- [x] 0.2 **[M1]** Schema: usulan ERD → approve → migrations (database-agnostic), models, factories, enums (`shops`, `price_lists`, `print_checks`, `reports`, kolom `role` dan `google_id` di `users`)
- [ ] 0.3 **[M1]** Seeder: akun admin dari `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env`, plus data shop contoh
- [ ] 0.4 **[M1]** `config/cariprint.php`, layout publik (terpisah dari layout dashboard starter kit), design tokens dan arah visual dengan rationale
- [ ] 0.5 **[M1]** Deploy versi awal ke `cariprint.lius.my.id` sesuai `docs/DEPLOY.md` (SQLite), SSL aktif, `scripts/deploy.sh` dibuat

Setelah 0.2 masuk `main`, Hanzelius dan Mario mulai mengerjakan task yang butuh database.

## Phase 1 — Directory (Hanzelius)

- [x] 1.1 **[M1]** Service `OpeningStatus` (buka/tutup/segera tutup) + unit test
- [ ] 1.2 **[M1]** Home: list shop dengan status buka, urut jarak garis lurus (Haversine) dari lokasi browser. Kalau izin lokasi ditolak, urut nama
- [ ] 1.3 **[M1]** Peta (`@vis.gl/react-google-maps`), marker sinkron dengan list
- [ ] 1.4 **[M1]** Detail shop: jam, layanan, daftar harga, tombol WhatsApp, badge terverifikasi/perkiraan, tanggal update
- [ ] 1.5 Jarak dan waktu tempuh via Routes API dari backend, dengan cache singkat

## Phase 2 — Migrasi ke Supabase (Bryan, setelah materi tutor)

- [ ] 2.1 Koneksi Supabase (`pgsql` via session pooler), `.env.example` diperbarui tanpa secret
- [ ] 2.2 Migration dijalankan di Supabase, data survei dipindahkan lewat seeder
- [ ] 2.3 Supabase Storage sebagai disk `s3` (private bucket)
- [ ] 2.4 Production pindah ke Supabase, SQLite tidak dipakai lagi

## Phase 3 — Auth dan quota (Bryan)

- [ ] 3.1 Login Google (Socialite), role `user`/`admin`
- [ ] 3.2 Sembunyikan menu ganti password untuk user Google
- [ ] 3.3 Quota AI: guest 3/hari, user 15/hari (RateLimiter)

## Phase 4 — AI Print Agent (Bryan)

- [ ] 4.1 **[M1 stretch]** `PrintSpec` + `PriceCalculator` + unit test
- [ ] 4.2 **[M1 stretch]** Prototype agent: kalimat user → `PrintSpec` (structured output Gemini), tampilkan hasil parsing + estimasi biaya
- [ ] 4.3 Rekomendasi: hitung biaya tiap shop, filter buka/preferensi, urutkan
- [ ] 4.4 Template pesan WhatsApp (link `wa.me`)

## Phase 5 — Admin dan laporan (Mario)

- [x] 5.1 **[M1]** Filament di `/admin`, hanya role admin
- [ ] 5.2 **[M1]** CRUD shop (jam, layanan, koordinat, `place_id`, aturan hitung halaman warna)
- [ ] 5.3 **[M1]** CRUD price list per shop
- [ ] 5.4 Form laporan untuk user login (dari halaman detail shop)
- [ ] 5.5 Review laporan di admin (pending / resolved / rejected)
- [ ] 5.6 **[M1]** Input data survei (lewat admin, atau CSV/seeder kalau datanya banyak)

## Phase 6 — Smart File Check (Bryan)

- [ ] 6.1 Upload PDF ke Supabase Storage (private) + hapus otomatis setelah 24 jam (scheduler)
- [ ] 6.2 pdf.js: jumlah halaman, preview, deteksi halaman berwarna dari analisis pixel
- [ ] 6.3 AI menilai halaman berwarna: perlu warna atau cukup B/W (structured output)
- [ ] 6.4 Hasil: saran halaman B/W + estimasi penghematan, user bisa override, tersimpan di `print_checks` untuk user login

## Phase 7 — Polish (semua)

- [ ] 7.1 Design pass seluruh halaman, empty/error/loading state
- [ ] 7.2 Tes di HP sungguhan, perbaiki layout 360px
- [ ] 7.3 Data survei final masuk production

## Keputusan terbuka

Putuskan bersama sebelum task terkait dikerjakan.

- Format file selain PDF (docx/pptx)? Usulan MVP: PDF saja.
- Batas ukuran file dan jumlah halaman.
- Batas area CitraLand (radius atau polygon).
- ~~Daftar layanan baku dan cara survei mencatat aturan "halaman warna" tiap shop.~~ Diputuskan: lihat [docs/SURVEY.md](SURVEY.md).
