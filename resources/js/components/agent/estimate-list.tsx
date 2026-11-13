import { Link } from '@inertiajs/react';
import { ChevronRight, Clock, MapPin } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { formatDistance, formatRupiah } from '@/lib/format';
import { serviceLabel } from '@/lib/labels';
import { show } from '@/routes/directory';
import type { OpenState } from '@/types';

export type Estimate = {
    name: string;
    slug: string;
    openState: OpenState;
    todayHours: string;
    distanceKm: number | null;
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

/** Termurah di atas dan distabilo. Tiap kartu menampilkan status buka, jam hari ini, dan jarak dari lokasi pengguna. */
export function EstimateList({ estimates }: { estimates: Estimate[] }) {
    const cheapestSlug = estimates.find((shop) => shop.total !== null)?.slug;

    return (
        <ul className="divide-y overflow-hidden rounded-lg border bg-card">
            {estimates.map((shop) => (
                <li key={shop.slug}>
                    <Link
                        href={show({ shop: shop.slug }).url}
                        className="flex items-center justify-between gap-3 px-4 py-3.5 hover:bg-accent"
                    >
                        <div className="flex min-w-0 flex-col gap-1.5">
                            <div className="flex flex-wrap items-center gap-2">
                                <p className="truncate font-display text-base font-semibold">
                                    {shop.name}
                                </p>
                                {shop.slug === cheapestSlug && (
                                    <span className="rounded-sm bg-highlight/60 px-1.5 py-0.5 text-xs font-semibold text-foreground">
                                        Termurah
                                    </span>
                                )}
                            </div>
                            <div className="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                <OpenStatusBadge state={shop.openState} />
                                <span className="inline-flex items-center gap-1 tabular-nums">
                                    <Clock className="size-3.5" aria-hidden />
                                    {shop.todayHours}
                                </span>
                                {shop.distanceKm !== null && (
                                    <span className="inline-flex items-center gap-1 font-medium text-foreground tabular-nums">
                                        <MapPin
                                            className="size-3.5 text-primary"
                                            aria-hidden
                                        />
                                        {formatDistance(shop.distanceKm)}
                                    </span>
                                )}
                            </div>
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
