import { home } from '@/routes';
import { index as agent } from '@/routes/agent';
import { index as compare } from '@/routes/compare';
import { index as directory } from '@/routes/directory';
import { howItWorks } from '@/routes/info';

export type NavLink = { label: string; href: string };

/** Menu utama di header dan footer supaya navigasi di seluruh website selalu sama. */
export const mainNav: NavLink[] = [
    { label: 'Cari tempat', href: directory().url },
    { label: 'Bandingkan harga', href: compare().url },
    { label: 'Asisten cetak', href: agent().url },
    { label: 'Cara kerja', href: howItWorks().url },
];

export const homeUrl = home().url;
