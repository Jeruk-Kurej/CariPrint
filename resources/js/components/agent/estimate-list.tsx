import { Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { serviceLabel } from '@/lib/labels';
import { formatRupiah } from '@/lib/format';
import { show } from '@/routes/directory';

export type Estimate = {
    name: string;
    slug: string;
    total: number | null;
    missing: string[];
};

function missingLabel(missing: string[]): string {
    const names = missing.map((key) =>
        key.startsWith('print-')
            ? key === 'print-color'
                ? 'print warna'
                : 'print hitam-putih'
            : serviceLabel(key).toLowerCase(),
    );

    return `Tidak ada harga ${names.join(', ')}`;
}

/** Termurah di atas dan distabilo. Tempat yang tidak bisa mengerjakan ditulis apa yang kurang, bukan diberi harga. */
export function EstimateList({ estimates }: { estimates: Estimate[] }) {
    const cheapestSlug = estimates.find((shop) => shop.total !== null)?.slug;

    return (
        <ul className="divide-y overflow-hidden rounded-lg border bg-card">
            {estimates.map((shop) => (
                <li key={shop.slug}>
                    <Link
                        href={show({ shop: shop.slug }).url}
                        className="flex items-center justify-between gap-3 px-4 py-3 hover:bg-accent"
                    >
                        <div className="min-w-0">
                            <p className="truncate font-display font-semibold">
                                {shop.name}
                            </p>
                            {shop.slug === cheapestSlug && (
                                <p className="text-xs text-muted-foreground">
                                    Paling murah untuk permintaan ini
                                </p>
                            )}
                            {shop.total === null && (
                                <p className="text-xs text-muted-foreground">
                                    {missingLabel(shop.missing)}
                                </p>
                            )}
                        </div>
                        <div className="flex shrink-0 items-center gap-2">
                            {shop.total !== null && (
                                <span
                                    className={`rounded-sm px-1.5 font-display text-lg font-bold tabular-nums ${shop.slug === cheapestSlug ? 'bg-highlight text-highlight-foreground' : ''}`}
                                >
                                    {formatRupiah(shop.total)}
                                </span>
                            )}
                            <ChevronRight
                                className="size-4 text-muted-foreground"
                                aria-hidden
                            />
                        </div>
                    </Link>
                </li>
            ))}
        </ul>
    );
}
