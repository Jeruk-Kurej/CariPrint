import type { ReactNode } from 'react';

const tones = {
    plain: 'bg-secondary text-secondary-foreground',
    paper: 'bg-card/70 text-foreground',
};

const sizes = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-sm',
};

/** Pil kecil untuk menyebut layanan atau bagian spesifikasi. Satu gaya untuk semua halaman. */
export function Tag({
    children,
    tone = 'plain',
    size = 'md',
}: {
    children: ReactNode;
    tone?: keyof typeof tones;
    size?: keyof typeof sizes;
}) {
    return (
        <span
            className={`inline-flex rounded-full font-medium ${tones[tone]} ${sizes[size]}`}
        >
            {children}
        </span>
    );
}
