# Arah visual CariPrint

## Konsep: papan pengumuman kampus

Setiap tempat printing tampil seperti pamflet yang ditempel di papan pengumuman kampus. Kertasnya berwarna pastel, ada paku atau selotip di atasnya, dan di bagian bawah ada sobekan bergaris dengan harga, tombol WhatsApp, dan penanda peta. Tampilan ini dipilih karena pengguna utamanya mahasiswa yang sudah akrab dengan papan pengumuman dan fotokopi di sekitar kampus.

## Untuk siapa

Mahasiswa yang membandingkan tempat printing di HP, sering dalam keadaan buru-buru sebelum kelas.

## Yang paling ditonjolkan, dan kenapa

1. **Harga** mulai dari, dalam Rupiah yang besar. Ini alasan utama mahasiswa membandingkan. Harga diberi stabilo kuning.
2. **Status buka** (Buka, Segera tutup, Tutup). Ini pertanyaan pertama mahasiswa: "masih buka nggak?".
3. **Jarak**. Ditampilkan dekat status supaya dibaca bersama.

Alamat, layanan, jam lengkap, dan tombol WhatsApp mendukung tiga hal di atas dan dibuat lebih kecil.

## Warna

Semua warna didefinisikan sebagai token di `resources/css/app.css`. Komponen tidak boleh menulis kode warna langsung.

| Token                                                          | Nilai                          | Dipakai untuk                                       |
| -------------------------------------------------------------- | ------------------------------ | --------------------------------------------------- |
| `--primary` / `--brand`                                        | `#3558f0` (biru tinta bolpoin) | tombol utama, logo, menu aktif, fokus               |
| `--highlight`                                                  | `#ffd84d` (kuning stabilo)     | harga. Hanya untuk harga                            |
| `--foreground`                                                 | `#1d2147`                      | teks utama                                          |
| `--muted-foreground`                                           | `#55597e`                      | teks pendukung                                      |
| `--background`                                                 | `#fcfdff`                      | latar, dengan garis kotak tipis seperti kertas buku |
| `--note-yellow` / `--note-pink` / `--note-mint` / `--note-sky` | pastel                         | warna kertas pamflet dan sticky note                |
| `--status-open`                                                | `#137a4a` (hijau)              | status Buka                                         |
| `--status-closing`                                             | `#9a5b00` (cokelat-amber)      | status Segera tutup                                 |
| `--status-closed`                                              | `#55597e` (abu kebiruan)       | status Tutup                                        |

Aturan:

- Warna status hanya untuk status buka. Kuning stabilo hanya untuk harga.
- Warna pastel hanya untuk kertas, bukan untuk teks atau tombol.

## Tipografi

- **Gabarito** (tebal) untuk judul dan harga. Ini yang memberi kesan papan pengumuman.
- **Instrument Sans** untuk teks isi.

## Elemen visual

- **Pamflet**: kertas pastel dengan sudut sedikit miring, paku payung atau selotip di atas, dan sobekan bergaris dengan ikon gunting.
- **Sticky note**: dipakai untuk ajakan ke halaman lain. Pojok kanan bawahnya diberi lipatan.
- **Latar**: kertas buku bergaris kotak tipis, dipasang di layout publik.

Elemen-elemen ini adalah pengingat tema, bukan hiasan yang berdiri sendiri. Jika di satu halaman terasa terlalu ramai, kurangi jumlah elemennya, jangan menghapus data.

## Gerak

- Hanya satu gerakan otomatis: pamflet di hero masuk satu per satu saat halaman dibuka.
- Gerakan itu mati kalau pengguna memilih "kurangi gerakan" di perangkatnya (`motion-safe`).

## Responsif

- Dirancang untuk HP dulu (lebar 360 px), lalu diperlebar untuk layar besar.
- Tabel perbandingan harga boleh digeser ke samping di dalam kotaknya. Halaman tidak boleh melebar.
