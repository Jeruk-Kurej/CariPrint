# Arah visual CariPrint

## Untuk siapa

Mahasiswa yang membandingkan tempat printing di HP, sering dalam keadaan buru-buru sebelum kelas.

## Yang paling ditonjolkan, dan kenapa

1. **Status buka** (Buka / Segera tutup / Tutup). Ini pertanyaan pertama mahasiswa: "masih buka nggak?".
2. **Harga** mulai dari, dalam Rupiah yang besar dan jelas. Ini alasan utama mahasiswa membandingkan.
3. **Jarak**. Dipasang dekat harga supaya dibaca bersama.

Hal lain (alamat, layanan, jam lengkap, tombol WhatsApp) mendukung tiga hal di atas dan dibuat lebih kecil.

## Warna

- Warna hanya dipakai untuk **status** dan **sumber harga**, bukan hiasan.
- Hijau: buka. Kuning: segera tutup. Merah: tutup.
- Harga terverifikasi memakai warna brand (teal). Harga perkiraan memakai abu-abu, supaya beda dari data hasil survei tanpa perlu membaca tulisan kecil.
- Semua warna didefinisikan di `resources/css/app.css` (token `--brand`, `--status-*`, `--price-*`). Komponen memakai kelas Tailwind seperti `text-brand`, tidak boleh memakai kode hex sendiri.

## Tipografi dan ruang

- Font bawaan starter kit (Instrument Sans), tidak menambah font baru supaya halaman cepat dimuat.
- Ukuran teks utama minimal setara `text-sm` di layar 360px.
- Satu kolom di HP, dua kolom mulai `sm`.

## Komponen

- Header dan footer publik ada di `resources/js/layouts/public-layout.tsx`. Layout dashboard bawaan starter kit tidak dipakai untuk halaman publik.
- Pakai komponen `components/ui` yang sudah ada sebelum membuat yang baru.
