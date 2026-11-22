import { ChevronLeft, ChevronRight } from 'lucide-react';
import { pageItems } from '@/lib/pagination';

type Props = {
    page: number;
    totalPages: number;
    onChange: (page: number) => void;
};

const button =
    'inline-flex size-11 items-center justify-center rounded-full border-2 text-sm font-semibold tabular-nums transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-40';

/** Navigasi halaman bernomor. Tombol minimal 44px supaya mudah diketuk di ponsel. */
export function Pagination({ page, totalPages, onChange }: Props) {
    if (totalPages <= 1) {
        return null;
    }

    return (
        <nav
            aria-label="Halaman daftar tempat"
            className="flex flex-wrap items-center justify-center gap-2"
        >
            <button
                type="button"
                aria-label="Halaman sebelumnya"
                disabled={page === 1}
                onClick={() => onChange(page - 1)}
                className={`${button} border-foreground/15 bg-card hover:border-primary`}
            >
                <ChevronLeft className="size-5" aria-hidden />
            </button>
            {pageItems(page, totalPages).map((item, index) =>
                item === 'gap' ? (
                    <span
                        key={`gap-${index}`}
                        aria-hidden
                        className="px-1 text-muted-foreground"
                    >
                        …
                    </span>
                ) : (
                    <button
                        key={item}
                        type="button"
                        aria-label={`Halaman ${item}`}
                        aria-current={item === page ? 'page' : undefined}
                        onClick={() => onChange(item)}
                        className={`${button} ${item === page ? 'border-primary bg-primary text-primary-foreground' : 'border-foreground/15 bg-card hover:border-primary'}`}
                    >
                        {item}
                    </button>
                ),
            )}
            <button
                type="button"
                aria-label="Halaman berikutnya"
                disabled={page === totalPages}
                onClick={() => onChange(page + 1)}
                className={`${button} border-foreground/15 bg-card hover:border-primary`}
            >
                <ChevronRight className="size-5" aria-hidden />
            </button>
        </nav>
    );
}
