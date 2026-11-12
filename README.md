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
    <a href="docs/ROADMAP.md"><strong>Lihat Roadmap »</strong></a>
    <br />
    <br />
    <a href="https://cariprint.lius.my.id">🌐 Lihat Website</a>
    ·
    <a href="https://github.com/Jeruk-Kurev/CariPrint/issues/new?labels=bug">🐛 Laporkan Bug</a>
    ·
    <a href="https://github.com/Jeruk-Kurev/CariPrint/issues/new?labels=enhancement">✨ Request Fitur</a>
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
[![Leaflet][Leaflet-badge]][Leaflet-url]
[![SQLite][SQLite-badge]][SQLite-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- GETTING STARTED -->
## Memulai

### Prasyarat

Pastikan sudah terinstal:
- PHP >= 8.2
- Composer
- Node.js & npm

### Instalasi

1. Clone repo
   ```bash
   git clone https://github.com/Jeruk-Kurev/CariPrint.git
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

Lalu buka http://127.0.0.1:8000.

> **Untuk asisten AI:** isi `GEMINI_API_KEY` di `.env` dengan API key dari [Google AI Studio](https://aistudio.google.com).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- FEATURES -->
## Status Fitur

Status: ✅ selesai · 🚧 sebagian · ⏳ belum dikerjakan

| Fitur | Status | Keterangan |
|---|---|---|
| Daftar tempat printing dengan status buka (Buka, Segera tutup, Tutup) | ✅ | Bisa diurutkan dari yang terdekat atau termurah, dan difilter yang masih buka |
| Peta yang tersinkron dengan daftar | ✅ | Klik titik untuk membuka halaman tempatnya |
| Halaman detail tempat | ✅ | Jam buka, layanan, daftar harga, tombol WhatsApp |
| Halaman perbandingan harga | ✅ | Harga layanan yang sama dari semua tempat, dengan yang termurah ditandai |
| Panel admin (`/admin`) | ✅ | Kelola data tempat dan harga, hanya untuk admin |
| Impor data survei | 🚧 | Impor sudah jalan. Data tempat dari Google Maps sudah masuk (9 tempat), harga belum |
| Asisten cetak (AI) | 🚧 | Kalimat kebutuhan cetak diubah jadi spesifikasi, lalu dihitung estimasi per tempat. Estimasi di server menunggu kunci Gemini |
| Jarak dan waktu tempuh lewat Routes API | ⏳ | Saat ini jarak garis lurus |
| Rekomendasi dan template pesan WhatsApp dari asisten | ⏳ | |
| Login Google dan batas pemakaian AI | ⏳ | |
| Laporan data salah dari pengguna | ⏳ | |
| Unggah dan cek file PDF (halaman berwarna) | ⏳ | |
| Pindah database ke Supabase | ⏳ | Saat ini masih SQLite |

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- TEAM -->
## Tim

Angka di bawah dihitung dari commit asli di branch `main` (tanpa merge), per 11 November 2026.

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
      <sub>⭐ 38 commits</sub>
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
      <sub>⭐ 51 commits</sub>
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
      <sub>⭐ 31 commits</sub>
    </td>
  </tr>
</table>

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- DATA -->
## Data

- **Lokasi, alamat, nomor telepon, dan jam buka** diambil dari Google Maps dengan alat Apify.
- **Harga belum tersedia** dari sumber mana pun. Harga akan ditanyakan langsung ke pemilik tempat dan hanya ditampilkan setelah dikonfirmasi. Harga yang sudah dikonfirmasi diberi tanda *Terverifikasi*, sedangkan harga yang belum disurvei diberi tanda *Perkiraan*.
- Lokasi pengguna hanya dipakai untuk menghitung jarak dan tidak disimpan.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- DOCS -->
## Dokumen

- [docs/ROADMAP.md](docs/ROADMAP.md) — daftar task lengkap dan pembagiannya.
- [docs/SURVEY.md](docs/SURVEY.md) — format data survei.
- [docs/DEPLOY.md](docs/DEPLOY.md) — cara deploy ke server.
- [docs/DEMO.md](docs/DEMO.md) — naskah demo.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- MARKDOWN LINKS & IMAGES -->
[contributors-shield]: https://img.shields.io/github/contributors/Jeruk-Kurev/CariPrint.svg?style=for-the-badge
[contributors-url]: https://github.com/Jeruk-Kurev/CariPrint/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Jeruk-Kurev/CariPrint.svg?style=for-the-badge
[forks-url]: https://github.com/Jeruk-Kurev/CariPrint/network/members
[stars-shield]: https://img.shields.io/github/stars/Jeruk-Kurev/CariPrint.svg?style=for-the-badge
[stars-url]: https://github.com/Jeruk-Kurev/CariPrint/stargazers
[issues-shield]: https://img.shields.io/github/issues/Jeruk-Kurev/CariPrint.svg?style=for-the-badge
[issues-url]: https://github.com/Jeruk-Kurev/CariPrint/issues
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
[SQLite-badge]: https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white
[SQLite-url]: https://sqlite.org
