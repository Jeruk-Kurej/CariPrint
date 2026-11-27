<!-- Back to top anchor -->

<a id="readme-top"></a>

<!-- PROJECT SHIELDS -->

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stars][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/Jeruk-Kurej/CariPrint">
    <img src="https://cariprint.lius.my.id/favicon.ico" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">CariPrint</h3>

  <p align="center">
    Website untuk mahasiswa di sekitar CitraLand Surabaya yang ingin mencari tempat printing.
    <br />
    <a href="https://cariprint.lius.my.id">🌐 Lihat Website</a>
    ·
    <a href="https://github.com/Jeruk-Kurej/CariPrint/issues/new?labels=bug">🐛 Laporkan Bug</a>
    ·
    <a href="https://github.com/Jeruk-Kurej/CariPrint/issues/new?labels=enhancement">✨ Request Fitur</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Daftar Isi</summary>
  <ol>
    <li>
      <a href="#tentang-project">Tentang Project</a>
      <ul>
        <li><a href="#teknologi">Teknologi</a></li>
      </ul>
    </li>
    <li>
      <a href="#memulai">Memulai</a>
      <ul>
        <li><a href="#prasyarat">Prasyarat</a></li>
        <li><a href="#instalasi">Instalasi</a></li>
      </ul>
    </li>
    <li><a href="#status-fitur">Status Fitur</a></li>
    <li><a href="#tim">Tim</a></li>
    <li><a href="#data">Data</a></li>
    <li><a href="#dokumen">Dokumen</a></li>
  </ol>
</details>

---

<!-- ABOUT THE PROJECT -->

## Tentang Project

[![Screenshot CariPrint][product-screenshot]](https://cariprint.lius.my.id)

Website untuk mahasiswa di sekitar CitraLand Surabaya yang ingin mencari tempat printing: melihat mana yang sedang buka, membandingkan harga, dan mengetahui jaraknya dari lokasi kamu.

> Website ini hanya menghubungkan mahasiswa dengan tempat printing. **Tidak ada pemesanan dan pembayaran** di dalamnya.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- TECH STACK -->

### Teknologi

[![Laravel][Laravel-badge]][Laravel-url]
[![React][React-badge]][React-url]
[![TypeScript][TypeScript-badge]][TypeScript-url]
[![TailwindCSS][Tailwind-badge]][Tailwind-url]
[![Inertia][Inertia-badge]][Inertia-url]
[![Filament][Filament-badge]][Filament-url]
[![Gemini][Gemini-badge]][Gemini-url]
[![Leaflet][Leaflet-badge]][Leaflet-url]
[![SQLite][SQLite-badge]][SQLite-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- GETTING STARTED -->

## Memulai

### Prasyarat

Pastikan sudah terinstal:

- PHP >= 8.3
- Composer
- Node.js & npm

### Instalasi

1. Clone repo

    ```bash
    git clone https://github.com/Jeruk-Kurej/CariPrint.git
    cd CariPrint
    ```

2. Install dependencies PHP dan Node

    ```bash
    composer install
    npm install
    ```

3. Salin file environment dan generate app key

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Siapkan database dan jalankan seeder

    ```bash
    touch database/database.sqlite
    php artisan migrate
    php artisan db:seed
    ```

5. Build aset frontend dan jalankan server
    ```bash
    npm run build
    php artisan serve
    ```

Lalu buka http://127.0.0.1:8000. Halaman detail dan perbandingan harga akan kosong sampai harga dikonfirmasi, dan itu wajar.

> **Untuk asisten AI:** isi `GEMINI_API_KEY` di `.env` dengan API key dari [Google AI Studio](https://aistudio.google.com).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- FEATURES -->

## Status Fitur

Status: ✅ selesai · 🚧 sebagian · ⏳ belum dikerjakan · ❌ dibatalkan

| Fitur                                                                 | Status | Keterangan                                                                                                                                                                                                                                                                                                                                                                                                                              |
| --------------------------------------------------------------------- | ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Daftar tempat printing dengan status buka (Buka, Segera tutup, Tutup) | ✅     | Bisa diurutkan dari yang terdekat atau termurah, difilter (masih buka, layanan, jarak), dan dibagi per halaman                                                                                                                                                                                                                                                                                                                          |
| Peta yang tersinkron dengan daftar                                    | ✅     | Klik titik untuk membuka halaman tempatnya                                                                                                                                                                                                                                                                                                                                                                                              |
| Halaman detail tempat                                                 | ✅     | Jam buka, layanan, tombol WhatsApp, dan daftar harga dengan label Perkiraan atau Terverifikasi                                                                                                                                                                                                                                                                                                                                          |
| Halaman perbandingan harga                                            | 🚧     | Halaman sudah jadi dan berisi harga berlabel _Perkiraan_. Harga _Terverifikasi_ menunggu konfirmasi pemilik tempat                                                                                                                                                                                                                                                                                                                      |
| Halaman informasi (cara kerja, tentang, pertanyaan umum, privasi)     | ✅     | Menjelaskan cara kerja, sumber data, dan data apa yang disimpan                                                                                                                                                                                                                                                                                                                                                                         |
| Deploy otomatis (CI/CD)                                               | ✅     | Tes berjalan di setiap perubahan `main`. Setelah lulus, situs diperbarui otomatis                                                                                                                                                                                                                                                                                                                                                       |
| Panel admin (`/admin`)                                                | ✅     | Kelola tempat, harga, dan usulan; ada dasbor ringkasan. Hanya untuk admin                                                                                                                                                                                                                                                                                                                                                               |
| Impor data survei                                                     | 🚧     | Impor dari Apify sudah jalan. 908 tempat dari Google Maps sudah masuk (per 24 November 2026), harga asli belum                                                                                                                                                                                                                                                                                                                          |
| Asisten cetak (AI)                                                    | ✅     | Kalimat kebutuhan cetak diubah jadi spesifikasi, lalu estimasi per tempat dihitung dari daftar harga (bukan oleh AI). Bisa menunjuk halaman tertentu dan melampirkan PDF, menyaring yang masih buka, mengurutkan termurah atau terdekat, membuka WhatsApp dengan pesan yang sudah ditulis, dan mengubah kertas, rangkap, serta jilid di slip pesanan dengan estimasi dihitung ulang tanpa memakai kuota AI. Memerlukan `GEMINI_API_KEY` |
| Cek warna file PDF (Smart File Check)                                 | ✅     | File dibaca di perangkat pengguna dan tidak diunggah. Server menilai tiap halaman (perlu warna, mungkin cukup hitam-putih, atau hitam-putih) dari persentase warnanya, lalu menghitung hematnya. Pengguna memilih sendiri halaman berwarna                                                                                                                                                                                              |
| Batas pemakaian AI                                                    | ✅     | Tamu 3 permintaan per hari, pengguna yang masuk 15, admin tanpa batas. Reset tengah malam WIB                                                                                                                                                                                                                                                                                                                                           |
| Login Google                                                          | ✅     | Pengguna masuk dengan akun Google mana pun (aplikasi OAuth berstatus produksi). Admin masuk dengan email dan kata sandi                                                                                                                                                                                                                                                                                                                 |
| Halaman Akunku                                                        | ✅     | Menampilkan usulan tempat milik pengguna beserta statusnya (Menunggu, Diterima, Ditolak) dan catatan admin                                                                                                                                                                                                                                                                                                                              |
| Usul tempat baru dari pengunjung                                      | ✅     | Wajib masuk. Admin menerima email dan meninjau di panel admin. Pengusul menerima tanda terima dan email keputusan. Usulan tidak langsung tampil di website                                                                                                                                                                                                                                                                              |
| Rekomendasi dan pesan WhatsApp siap kirim dari asisten                | ✅     | Estimasi tiap tempat diurutkan, dan tombol Tanya via WA membuka WhatsApp dengan pesan yang sudah ditulis                                                                                                                                                                                                                                                                                                                                |
| Jarak dan waktu tempuh lewat Routes API                               | ❌     | Dibatalkan karena butuh billing. Jarak tetap garis lurus                                                                                                                                                                                                                                                                                                                                                                                |
| Deteksi area baru otomatis (revisi dosen)                             | ⏳     | Belum dikerjakan                                                                                                                                                                                                                                                                                                                                                                                                                        |
| Laporan data salah dari pengguna                                      | ✅     | Pengguna yang masuk memilih jenis masalah dan menulis pesan dari halaman tempat. Admin meninjau di panel admin dan pelapor melihat hasilnya di halaman Akunku                                                                                                                                                                                                                                                                           |
| Hubungi kami                                                          | ✅     | Formulir untuk soal akun, data pribadi, dan saran. Pesan masuk ke panel admin dan email tim, lalu dibalas lewat email. Tidak ada alamat email yang ditampilkan di situs                                                                                                                                                                                                                                                                 |
| Cari tempat lewat nama atau alamat                                    | ✅     | Misalnya "ngagel". Berlaku di beranda, Cari tempat, dan Bandingkan                                                                                                                                                                                                                                                                                                                                                                      |
| Filter ukuran kertas dan warna                                        | ✅     | Harga di kartu dan urutan Termurah mengikuti pilihan, dengan tanda Terverifikasi atau Perkiraan. Filter ikut tersimpan di alamat halaman                                                                                                                                                                                                                                                                                                |
| Pindah database ke Supabase                                           | ✅     | Web live sudah memakai Supabase sejak 27 Nov 2026                                                                                                                                                                                                                                                                                                                                                                                       |
| Hapus akun sendiri                                                    | ✅     | Dari halaman Akunku, dikonfirmasi dengan mengetik email. Usulan tempat dari email itu ikut terhapus                                                                                                                                                                                                                                                                                                                                     |
| Halaman error berbahasa Indonesia                                     | ✅     | 403, 404, 419, 500, dan 503 punya halaman dengan jalan kembali ke beranda                                                                                                                                                                                                                                                                                                                                                               |
| Pratinjau tautan di WhatsApp                                          | ✅     | Judul, deskripsi, dan gambar muncul saat tautan dibagikan, termasuk pratinjau khusus tiap tempat                                                                                                                                                                                                                                                                                                                                        |

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- TEAM -->

## Tim

<table width="100%">
  <tr>
    <td align="center" valign="top" width="33%">
      <a href="https://github.com/Jeruk-Kurej">
        <img src="https://avatars.githubusercontent.com/Jeruk-Kurej?size=100" width="80" height="80" style="border-radius:50%;object-fit:cover;" alt="Bryan"/>
      </a>
      <br /><br />
      <a href="https://github.com/Jeruk-Kurej"><b>Bryan Carlie Lukito Setiawan</b></a>
      <br />
      <sub>Fondasi project, desain, asisten AI, deploy</sub>
      <br />
    </td>
    <td align="center" valign="top" width="34%">
      <a href="https://github.com/Infinite-cpu-ai">
        <img src="https://avatars.githubusercontent.com/Infinite-cpu-ai?size=100" width="80" height="80" style="border-radius:50%;object-fit:cover;" alt="Hanzelius"/>
      </a>
      <br /><br />
      <a href="https://github.com/Infinite-cpu-ai"><b>Hanzelius</b></a>
      <br />
      <sub>Direktori tempat — daftar, peta, detail, perbandingan</sub>
      <br />
    </td>
    <td align="center" valign="top" width="33%">
      <a href="https://github.com/mario-r-a">
        <img src="https://avatars.githubusercontent.com/mario-r-a?size=100" width="80" height="80" style="border-radius:50%;object-fit:cover;" alt="Mario"/>
      </a>
      <br /><br />
      <a href="https://github.com/mario-r-a"><b>Mario Ruby Ariesusandi</b></a>
      <br />
      <sub>Panel admin, validasi data, pembaca foto harga</sub>
      <br />
    </td>
  </tr>
</table>

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- DATA -->

## Data

- **Lokasi, alamat, nomor telepon, dan jam buka** diambil dari Google Maps dengan alat Apify.
- **Harga belum tersedia** dari sumber mana pun. Harga akan ditanyakan langsung ke pemilik tempat dan hanya ditampilkan setelah dikonfirmasi. Harga yang sudah dikonfirmasi diberi tanda _Terverifikasi_, sedangkan harga yang belum disurvei diberi tanda _Perkiraan_.
- **Harga perkiraan:** `EstimatedPriceSeeder` mengisi harga perkiraan agar halaman perbandingan dan asisten punya data. Harga itu bukan hasil survei, disimpan dengan sumber `estimated` dan ditampilkan dengan label _Perkiraan_. Seeder tidak menyentuh tempat yang sudah punya harga terverifikasi.
- Lokasi pengguna hanya dipakai untuk menghitung jarak dan tidak disimpan.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- DOCS -->

## Dokumen

- [docs/SURVEY.md](docs/SURVEY.md) — format data survei.
- [docs/DEPLOY.md](docs/DEPLOY.md) — cara deploy ke server.
- [docs/HANDOFF.md](docs/HANDOFF.md) — status terkini, keputusan, dan sisa pekerjaan untuk anggota tim.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- MARKDOWN LINKS & IMAGES -->

[contributors-shield]: https://img.shields.io/github/contributors/Jeruk-Kurej/CariPrint.svg?style=for-the-badge
[contributors-url]: https://github.com/Jeruk-Kurej/CariPrint/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Jeruk-Kurej/CariPrint.svg?style=for-the-badge
[forks-url]: https://github.com/Jeruk-Kurej/CariPrint/network/members
[stars-shield]: https://img.shields.io/github/stars/Jeruk-Kurej/CariPrint.svg?style=for-the-badge
[stars-url]: https://github.com/Jeruk-Kurej/CariPrint/stargazers
[issues-shield]: https://img.shields.io/github/issues/Jeruk-Kurej/CariPrint.svg?style=for-the-badge
[issues-url]: https://github.com/Jeruk-Kurej/CariPrint/issues
[product-screenshot]: docs/landing_page.png
[Laravel-badge]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[React-badge]: https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB
[React-url]: https://reactjs.org
[TypeScript-badge]: https://img.shields.io/badge/TypeScript-3178C6?style=for-the-badge&logo=typescript&logoColor=white
[TypeScript-url]: https://www.typescriptlang.org
[Tailwind-badge]: https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white
[Tailwind-url]: https://tailwindcss.com
[Inertia-badge]: https://img.shields.io/badge/Inertia.js-9553E9?style=for-the-badge&logo=inertia&logoColor=white
[Inertia-url]: https://inertiajs.com
[Leaflet-badge]: https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white
[Leaflet-url]: https://leafletjs.com
[Filament-badge]: https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white
[Filament-url]: https://filamentphp.com
[Gemini-badge]: https://img.shields.io/badge/Gemini-8E75B2?style=for-the-badge&logo=googlegemini&logoColor=white
[Gemini-url]: https://ai.google.dev
[SQLite-badge]: https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white
[SQLite-url]: https://sqlite.org
