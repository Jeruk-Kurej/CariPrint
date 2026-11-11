import { Link } from '@inertiajs/react';
import { ChevronRight, MapPin } from 'lucide-react';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { WhatsAppButton } from '@/components/directory/whatsapp-button';
import { Button } from '@/components/ui/button';
import { formatDistance, formatRupiah } from '@/lib/format';
import { show } from '@/routes/directory';
import type { ShopSummary } from '@/types';

type Props = {
    shop: ShopSummary;
    selected: boolean;
    onSelect: () => void;
};

/** Satu baris papan harga: nama dan harga di atas, status dan aksi di bawah. */
export function ShopRow({ shop, selected, onSelect }: Props) {
    return (
        <article className={`grid gap-3 p-4 ${selected ? 'bg-accent' : ''}`}>
            <div className="flex items-start justify-between gap-4">
                <button
                    type="button"
                    onClick={onSelect}
                    aria-pressed={selected}
                    className="min-w-0 text-left"
                >
                    <h2 className="truncate font-display text-lg font-semibold">
                        {shop.name}
                    </h2>
                    <p className="truncate text-sm text-muted-foreground">
                        {shop.address}
                    </p>
                </button>
                <div className="shrink-0 text-right">
                    <p className="text-xs text-muted-foreground">mulai dari</p>
                    <p className="mt-0.5 inline-block rounded-sm bg-highlight px-1.5 font-display text-xl font-bold text-highlight-foreground tabular-nums">
                        {shop.startingPrice === null
                            ? '–'
                            : formatRupiah(shop.startingPrice)}
                    </p>
                </div>
            </div>

            <div className="flex flex-wrap items-center justify-between gap-2">
                <div className="flex items-center gap-3 text-sm">
                    <OpenStatusBadge state={shop.openState} />
                    {shop.distanceKm !== null && (
                        <span className="flex items-center gap-1 font-medium text-muted-foreground tabular-nums">
                            <MapPin className="size-3.5" aria-hidden />
                            {formatDistance(shop.distanceKm)}
                        </span>
                    )}
                </div>
                <div className="flex items-center gap-1">
                    {shop.whatsapp && <WhatsAppButton phone={shop.whatsapp} />}
                    <Button asChild size="sm" variant="ghost">
                        <Link href={show({ shop: shop.slug }).url}>
                            Detail
                            <ChevronRight aria-hidden />
                        </Link>
                    </Button>
                </div>
            </div>
        </article>
    );
}
