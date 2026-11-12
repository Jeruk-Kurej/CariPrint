# Template Prompt Claude Code

Bagian dalam `<...>` diganti sesuai task. Semua prompt dijalankan dari folder project CariPrint.

## 1. Mengerjakan satu task

```
Lanjut task <X.Y> di ROADMAP. Buat branch dari `main` yang terbaru, dengan nama sesuai pemilik task: `bryan`, `hanzelius`, atau `mario`.

1. Plan dulu: file yang akan dibuat atau diubah, keputusan desain beserta alasannya, dan pertanyaan kalau ada yang ambigu. Tunggu approve dariku.
2. Implement beserta test-nya.
3. Jalankan semua check di guidelines sampai lulus.
4. Update checkbox task di ROADMAP dan tabel progress di README, usulkan pesan commit, lalu tunggu OK.
```

## 2. Merge ke main

```
Task di branch ini sudah selesai. Sebelum merge, pastikan tim sudah setuju. Lalu:
1. Merge `main` terbaru ke branch ini, dan jalankan semua check di guidelines sampai lulus.
2. Buat commit dengan identitas git-ku sendiri (bukan orang lain).
3. Buka Pull Request ke `main` dan tunggu tes CI hijau.
4. Setelah disetujui, merge lewat GitHub. Setelah `main` berubah, deploy berjalan otomatis (lihat docs/DEPLOY.md).
Jangan push langsung ke `main`.
```

## 3. Lanjut di sesi baru

```
Lanjut project CariPrint. Baca `docs/ROADMAP.md`, cek `git status` dan branch aktif, lalu rangkum posisi kita sekarang dan task berikutnya. Jangan mulai mengerjakan sebelum aku konfirmasi.
```

## 4. Review kode teman sebelum merge

```
Review perubahan di branch `<nama-branch>` dibanding `main`. Cek juga keterkaitan dengan dokumen di `docs/` (README, ROADMAP, DEPLOY, SURVEY) supaya tetap sesuai. Cek kesesuaian dengan `.ai/guidelines/cariprint.md`: arsitektur, duplikasi, kode yang tidak terpakai, test, dan aturan inti (AI tidak menghitung harga, tidak ada data Google selain `place_id`, dll.). Laporkan temuan berdasarkan tingkat keparahan. Jangan ubah apa pun.
```

## 5. Bingung mau ngapain

```
Aku bingung langkah berikutnya. Baca `docs/ROADMAP.md` dan kondisi repo, lalu beri aku langkah bernomor: mana yang aku kerjakan manual dan mana yang kamu kerjakan, lengkap dengan prompt yang sesuai dari `docs/PROMPTS.md`.
```
