import { home } from '@/routes';
import { index as agent } from '@/routes/agent';
import { index as compare } from '@/routes/compare';
import { index as directory } from '@/routes/directory';
import { about, faq, howItWorks } from '@/routes/info';

export type NavLink = { label: string; href: string };

/** Menu utama di header. Footer memakai daftar yang sama supaya tidak ada tautan yang terlewat. */
export const mainNav: NavLink[] = [
    { label: 'Cari tempat', href: directory().url },
    { label: 'Bandingkan harga', href: compare().url },
    { label: 'Asisten cetak', href: agent().url },
    { label: 'Cara kerja', href: howItWorks().url },
];

export const aboutNav: NavLink[] = [
    { label: 'Cara kerja', href: howItWorks().url },
    { label: 'Pertanyaan umum', href: faq().url },
    { label: 'Tentang', href: about().url },
];

export const homeUrl = home().url;
