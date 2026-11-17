import { home } from '@/routes';
import { index as agent } from '@/routes/agent';
import { index as compare } from '@/routes/compare';
import { index as directory } from '@/routes/directory';
import { about, faq, howItWorks, privacy } from '@/routes/info';
import { create as suggest } from '@/routes/suggestions';

export type NavLink = { label: string; href: string };

/** Menu utama di header dan footer supaya navigasi di seluruh website selalu sama. */
export const mainNav: NavLink[] = [
    { label: 'Cari tempat', href: directory().url },
    { label: 'Bandingkan harga', href: compare().url },
    { label: 'Asisten cetak', href: agent().url },
    { label: 'Cara kerja', href: howItWorks().url },
];

/** Halaman penjelasan dan formulir usul tempat, ditautkan dari footer supaya selalu bisa ditemukan. */
export const infoNav: NavLink[] = [
    { label: 'Cara kerja', href: howItWorks().url },
    { label: 'Pertanyaan umum', href: faq().url },
    { label: 'Tentang', href: about().url },
    { label: 'Privasi', href: privacy().url },
    { label: 'Usulkan tempat', href: suggest().url },
];

export const homeUrl = home().url;
