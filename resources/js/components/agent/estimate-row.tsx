import { Link } from '@inertiajs/react';
import { Clock, MapPin, MessageCircle } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { formatDistance, formatRupiah } from '@/lib/format';
import { serviceLabel } from '@/lib/labels';
import { whatsappUrl } from '@/lib/whatsapp';
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
    cheapest: boolean;
    whatsapp: string | null;
    whatsappMessage: string | null;
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

/** Satu tempat di hasil estimasi. Seluruh baris membuka halaman tempat, tombol WhatsApp membawa pesan yang sudah ditulis. */
export function EstimateRow({ shop }: { shop: Estimate }) {
    return (
        <li className="relative flex items-center justify-between gap-3 px-4 py-3.5 hover:bg-accent">
            <div className="flex min-w-0 flex-col gap-1.5">
                <div className="flex flex-wrap items-center gap-2">
                    <p className="truncate font-display text-base font-semibold">
                        <Link
                            href={show({ shop: shop.slug }).url}
                            className="after:absolute after:inset-0"
                        >
                            {shop.name}
                        </Link>
                    </p>
                    {shop.cheapest && (
                        <span className="rounded-sm bg-secondary px-1.5 py-0.5 text-xs font-semibold text-foreground">
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
            <div className="flex shrink-0 flex-col items-end gap-2">
                {shop.total !== null && (
                    <span
                        className={`rounded-sm px-1.5 font-display text-lg font-bold tabular-nums ${shop.cheapest ? 'bg-highlight text-highlight-foreground' : ''}`}
                    >
                        {formatRupiah(shop.total)}
                    </span>
                )}
                {shop.whatsapp && shop.whatsappMessage && (
                    <a
                        href={whatsappUrl(shop.whatsapp, shop.whatsappMessage)}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="relative z-10 inline-flex min-h-11 items-center gap-1.5 rounded-full border-2 border-foreground/15 bg-card px-3 text-sm font-semibold hover:border-primary hover:text-primary focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    >
                        <MessageCircle className="size-4" aria-hidden />
                        Tanya via WA
                        <span className="sr-only"> (tab baru)</span>
                    </a>
                )}
            </div>
        </li>
    );
}
