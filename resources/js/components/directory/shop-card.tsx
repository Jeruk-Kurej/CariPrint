import { MapPin } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { OpenStatusBadge } from '@/components/directory/open-status-badge';
import { WhatsAppButton } from '@/components/directory/whatsapp-button';
import { formatDistance, formatRupiah } from '@/lib/format';
import type { ShopSummary } from '@/types';

type Props = {
    shop: ShopSummary;
    selected?: boolean;
    onSelect?: () => void;
};

export function ShopCard({ shop, selected = false, onSelect }: Props) {
    return (
        <article
            className={`flex flex-col gap-3 rounded-xl border bg-card p-4 text-card-foreground shadow-sm ${selected ? 'ring-2 ring-brand' : ''}`}
        >
            <header className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <h2 className="truncate font-semibold">{shop.name}</h2>
                    <p className="truncate text-sm text-muted-foreground">
                        {shop.address}
                    </p>
                </div>
                <OpenStatusBadge state={shop.openState} />
            </header>

            <dl className="flex items-end justify-between gap-3">
                <div>
                    <dt className="text-xs text-muted-foreground">
                        Mulai dari
                    </dt>
                    <dd className="text-lg font-semibold">
                        {shop.startingPrice === null
                            ? '–'
                            : `${formatRupiah(shop.startingPrice)}/lbr`}
                    </dd>
                </div>
                {shop.distanceKm !== null && (
                    <div className="flex items-center gap-1 text-sm font-medium">
                        <dt className="sr-only">Jarak</dt>
                        <MapPin className="size-4" aria-hidden />
                        <dd>{formatDistance(shop.distanceKm)}</dd>
                    </div>
                )}
            </dl>

            <footer className="flex justify-end gap-2">
                {onSelect && (
                    <Button size="sm" variant="ghost" onClick={onSelect}>
                        Lihat di peta
                    </Button>
                )}
                {shop.whatsapp && <WhatsAppButton phone={shop.whatsapp} />}
            </footer>
        </article>
    );
}
