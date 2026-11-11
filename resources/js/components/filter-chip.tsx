import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';

const base =
    'inline-flex items-center rounded-full border-2 px-4 py-1.5 text-sm font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50';
const idle =
    'border-foreground/15 bg-card hover:border-primary hover:text-primary';
const active = 'border-primary bg-primary text-primary-foreground';

type Props = { active?: boolean; children: ReactNode } & (
    | { href: string }
    | { onClick: () => void; disabled?: boolean }
);

/** Pil kecil untuk memilih urutan atau filter. Jadi tautan kalau punya href, tombol kalau punya onClick. */
export function FilterChip(props: Props) {
    const className = `${base} ${props.active ? active : idle}`;

    if ('href' in props) {
        return (
            <Link href={props.href} className={className}>
                {props.children}
            </Link>
        );
    }

    return (
        <button
            type="button"
            onClick={props.onClick}
            disabled={props.disabled}
            aria-pressed={props.active ?? false}
            className={className}
        >
            {props.children}
        </button>
    );
}
