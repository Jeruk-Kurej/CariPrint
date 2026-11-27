import { Badge } from '@/components/ui/badge';
import type { ShopPrice } from '@/types/shop';

/** Tells the visitor whether a price came from our survey or is only an estimate. */
export function PriceSourceBadge({ source }: { source: ShopPrice['source'] }) {
    if (source === 'verified') {
        return (
            <Badge
                variant="outline"
                className="border-status-open/40 bg-status-open/10 text-status-open"
            >
                Terverifikasi
            </Badge>
        );
    }

    return (
        <Badge variant="outline" className="bg-card text-price-estimated">
            Perkiraan
        </Badge>
    );
}
