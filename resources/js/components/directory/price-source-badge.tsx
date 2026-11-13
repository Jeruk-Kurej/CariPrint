import { Badge } from '@/components/ui/badge';
import type { ShopPrice } from '@/types/shop';

/** Tells the visitor whether a price came from our survey or is only an estimate. */
export function PriceSourceBadge({ source }: { source: ShopPrice['source'] }) {
    if (source === 'verified') {
        return (
            <Badge className="bg-price-verified text-brand-foreground">
                Terverifikasi
            </Badge>
        );
    }

    return (
        <Badge variant="outline" className="text-price-estimated">
            Perkiraan
        </Badge>
    );
}
