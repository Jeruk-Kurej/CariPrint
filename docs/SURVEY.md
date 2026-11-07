# Format Survei Tempat Printing

Panduan untuk mencatat data survei supaya data dari tiga orang bisa langsung digabung dan diimpor ke database lewat seeder.

Isi dua file CSV dari template:

- `docs/survey-template-shops.csv`: satu baris per tempat printing.
- `docs/survey-template-prices.csv`: satu baris per harga.

Simpan hasilnya dengan nama yang sama tanpa kata `template`, lalu kirim ke Mario.

## Daftar layanan baku

Kolom `services` dan `service` hanya boleh berisi kode di bawah ini.

| Kode                | Nama di website | Satuan harga         |
| ------------------- | --------------- | -------------------- |
| `print`             | Print           | per halaman (`page`) |
| `photocopy`         | Fotokopi        | per halaman (`page`) |
| `scan`              | Scan            | per halaman (`page`) |
| `laminating`        | Laminating      | per lembar (`item`)  |
| `spiral_binding`    | Jilid spiral    | per buku (`item`)    |
| `tape_binding`      | Jilid lakban    | per buku (`item`)    |
| `hardcover_binding` | Jilid hardcover | per buku (`item`)    |

Kalau menemukan layanan lain yang sering ada, catat di kolom catatan dan kabari tim. Layanan baru harus disepakati dulu sebelum dipakai.

## Data tempat printing (`survey-template-shops.csv`)

| Kolom             | Isi                                                                                      | Contoh                                   |
| ----------------- | ---------------------------------------------------------------------------------------- | ---------------------------------------- |
| `slug`            | Nama pendek huruf kecil, pakai tanda hubung. Harus unik                                  | `print-sejahtera`                        |
| `name`            | Nama tempat sesuai papan nama                                                            | `Print Sejahtera`                        |
| `address`         | Alamat lengkap                                                                           | `Ruko Taman Gapura Blok A-5, CitraLand`  |
| `latitude`        | Dari Google Maps: klik kanan titik lokasi → klik angka koordinat (angka pertama)         | `-7.2875310`                             |
| `longitude`       | Angka kedua dari koordinat yang sama                                                     | `112.6370120`                            |
| `whatsapp`        | Nomor WhatsApp format internasional tanpa `+` dan spasi. Kosongkan kalau tidak ada       | `6281234567890`                          |
| `place_id`        | Kosongkan. Diisi admin nanti                                                             |                                          |
| `mon` … `sun`     | Jam buka per hari, format `HH:MM-HH:MM`. Istirahat dipisah `\|`. Tutup seharian: `tutup` | `08:00-12:00\|13:00-21:00`               |
| `services`        | Kode layanan yang tersedia, dipisah `\|`                                                 | `print\|photocopy\|spiral_binding`       |
| `color_page_rule` | Cara tempat itu menghitung halaman warna, tulis apa adanya dari penjelasan penjaga       | `Ada logo berwarna tetap dihitung warna` |
| `surveyed_at`     | Tanggal survei, format `YYYY-MM-DD`                                                      | `2026-11-06`                             |
| `surveyor`        | Nama yang survei                                                                         | `Mario`                                  |

Buka 24 jam ditulis `00:00-24:00`. Kalau jam tutup lewat tengah malam, tulis apa adanya, misalnya `18:00-02:00`.

## Daftar harga (`survey-template-prices.csv`)

| Kolom        | Isi                                                            | Contoh            |
| ------------ | -------------------------------------------------------------- | ----------------- |
| `shop_slug`  | Sama dengan `slug` di file shops                               | `print-sejahtera` |
| `service`    | Kode layanan                                                   | `print`           |
| `paper_size` | `a4`, `f4`, atau `a3`. Kosongkan untuk jilid dan laminating    | `a4`              |
| `color_mode` | `bw` (hitam-putih) atau `color`. Kosongkan kalau tidak berlaku | `bw`              |
| `price`      | Harga dalam Rupiah, angka bulat tanpa titik                    | `500`             |
| `unit`       | `page` atau `item`, sesuai tabel layanan baku                  | `page`            |

Satu kombinasi layanan, ukuran kertas, dan mode warna = satu baris. Contoh: print A4 B/W dan print A4 warna ditulis dua baris.

## Tips di lapangan

- Foto papan harga kalau diizinkan, supaya bisa dicek ulang.
- Tanyakan cara menghitung halaman warna dengan contoh: "Kalau cuma logo kampus yang berwarna, dihitung warna atau hitam-putih?"
- Catat jam buka hari Minggu dan hari libur.
