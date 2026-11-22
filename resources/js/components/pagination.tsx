import { ChevronLeft, ChevronRight } from 'lucide-react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { pageItems } from '@/lib/pagination';

type Props = {
    page: number;
    totalPages: number;
    onChange: (page: number) => void;
};

const button =
    'size-11 items-center justify-center rounded-full border-2 text-sm font-semibold tabular-nums transition-colors focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:pointer-events-none disabled:opacity-40';

const JUMP_FROM_PAGES = 7;

/** Kolom "Ke halaman", hanya untuk daftar yang cukup panjang. */
function PageJump({
    totalPages,
    onChange,
}: Pick<Props, 'totalPages' | 'onChange'>) {
    const [value, setValue] = useState('');

    function submit(event: FormEvent) {
        event.preventDefault();

        const target = Math.round(Number(value));

        if (target >= 1 && target <= totalPages) {
            onChange(target);
            setValue('');
        }
    }

    return (
        <form
            onSubmit={submit}
            className="flex items-center justify-center gap-2 text-sm"
        >
            <label htmlFor="ke-halaman" className="font-semibold">
                Ke halaman
            </label>
            <input
                id="ke-halaman"
                type="number"
                inputMode="numeric"
                min={1}
                max={totalPages}
                value={value}
                onChange={(event) => setValue(event.target.value)}
                placeholder={`1–${totalPages}`}
                className="min-h-11 w-24 rounded-full border-2 border-foreground/15 bg-background px-3 text-center tabular-nums outline-none focus-visible:ring-2 focus-visible:ring-ring"
            />
            <button
                type="submit"
                className="min-h-11 rounded-full border-2 border-foreground/15 bg-card px-4 font-semibold transition-colors hover:border-primary focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            >
                Buka
            </button>
        </form>
    );
}

/** Navigasi halaman bernomor. Tombol minimal 44px supaya mudah diketuk di ponsel. */
export function Pagination({ page, totalPages, onChange }: Props) {
    if (totalPages <= 1) {
        return null;
    }

    return (
        <div className="flex flex-col gap-3">
            <nav
                aria-label="Halaman daftar tempat"
                className="flex flex-wrap items-center justify-center gap-2"
            >
                <button
                    type="button"
                    aria-label="Halaman sebelumnya"
                    disabled={page === 1}
                    onClick={() => onChange(page - 1)}
                    className={`${button} inline-flex border-foreground/15 bg-card hover:border-primary`}
                >
                    <ChevronLeft className="size-5" aria-hidden />
                </button>
                <span
                    aria-live="polite"
                    className="min-w-24 text-center text-sm font-semibold tabular-nums sm:hidden"
                >
                    Hal. {page} dari {totalPages}
                </span>
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
                            className={`${button} hidden sm:inline-flex ${item === page ? 'border-primary bg-primary text-primary-foreground' : 'border-foreground/15 bg-card hover:border-primary'}`}
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
                    className={`${button} inline-flex border-foreground/15 bg-card hover:border-primary`}
                >
                    <ChevronRight className="size-5" aria-hidden />
                </button>
            </nav>
            {totalPages > JUMP_FROM_PAGES && (
                <PageJump totalPages={totalPages} onChange={onChange} />
            )}
        </div>
    );
}
